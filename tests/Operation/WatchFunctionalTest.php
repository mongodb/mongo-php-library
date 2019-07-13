<?php

namespace MongoDB\Tests\Operation;

use MongoDB\ChangeStream;
use MongoDB\BSON\TimestampInterface;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\LogicException;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Exception\ResumeTokenException;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\Watch;
use MongoDB\Tests\CommandObserver;
use stdClass;
use ReflectionClass;

class WatchFunctionalTest extends FunctionalTestCase
{
    private static $wireVersionForStartAtOperationTime = 7;

    private $defaultOptions = ['maxAwaitTimeMS' => 500];

    public function setUp()
    {
        parent::setUp();

        $this->skipIfChangeStreamIsNotSupported();
        $this->createCollection();
    }

    /**
     * Prose test: "ChangeStream must continuously track the last seen
     * resumeToken"
     */
    public function testGetResumeToken()
    {
        if ($this->isPostBatchResumeTokenSupported()) {
            $this->markTestSkipped('postBatchResumeToken is supported');
        }

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->getResumeToken());

        $this->insertDocument(['x' => 1]);
        $this->insertDocument(['x' => 2]);

        $changeStream->next();
        $this->assertSameDocument($changeStream->current()->_id, $changeStream->getResumeToken());

        $changeStream->next();
        $this->assertSameDocument($changeStream->current()->_id, $changeStream->getResumeToken());

        $this->insertDocument(['x' => 3]);

        $changeStream->next();
        $this->assertSameDocument($changeStream->current()->_id, $changeStream->getResumeToken());
    }

    /**
     * Prose test: "ChangeStream must continuously track the last seen
     * resumeToken"
     */
    public function testGetResumeTokenWithPostBatchResumeToken()
    {
        if ( ! $this->isPostBatchResumeTokenSupported()) {
            $this->markTestSkipped('postBatchResumeToken is not supported');
        }

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);

        $events = [];

        (new CommandObserver)->observe(
            function() use ($operation, &$changeStream) {
                $changeStream = $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use (&$events) {
                $events[] = $event;
            }
        );

        $this->assertCount(1, $events);
        $this->assertSame('aggregate', $events[0]['started']->getCommandName());
        $postBatchResumeToken = $this->getPostBatchResumeTokenFromReply($events[0]['succeeded']->getReply());

        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());
        $this->assertSameDocument($postBatchResumeToken, $changeStream->getResumeToken());

        $this->insertDocument(['x' => 1]);
        $this->insertDocument(['x' => 2]);

        $events = [];

        (new CommandObserver)->observe(
            function() use ($changeStream) {
                $changeStream->next();
            },
            function (array $event) use (&$events) {
                $events[] = $event;
            }
        );

        $this->assertCount(1, $events);
        $this->assertSame('getMore', $events[0]['started']->getCommandName());
        $postBatchResumeToken = $this->getPostBatchResumeTokenFromReply($events[0]['succeeded']->getReply());

        $changeStream->next();
        $this->assertSameDocument($changeStream->current()->_id, $changeStream->getResumeToken());

        $changeStream->next();
        $this->assertSameDocument($postBatchResumeToken, $changeStream->getResumeToken());
    }

    /**
     * Prose test: "ChangeStream will resume after a killCursors command is
     * issued for its child cursor."
     */
    public function testNextResumesAfterCursorNotFound()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 1, 'x' => 'foo'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 1],
        ];

        $this->assertMatchesDocument($expectedResult, $changeStream->current());

        $this->killChangeStreamCursor($changeStream);

        $this->insertDocument(['_id' => 2, 'x' => 'bar']);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 2, 'x' => 'bar'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 2]
        ];

        $this->assertMatchesDocument($expectedResult, $changeStream->current());
    }

    public function testNextResumesAfterConnectionException()
    {
        /* In order to trigger a dropped connection, we'll use a new client with
         * a socket timeout that is less than the change stream's maxAwaitTimeMS
         * option. */
        $manager = new Manager(static::getUri(), ['socketTimeoutMS' => 50]);
        $primaryServer = $manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        $operation = new Watch($manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($primaryServer);
        $changeStream->rewind();

        $commands = [];

        (new CommandObserver)->observe(
            function() use ($changeStream) {
                $changeStream->next();
            },
            function(array $event) use (&$commands) {
                $commands[] = $event['started']->getCommandName();
            }
        );

        $expectedCommands = [
            /* The initial aggregate command for change streams returns a cursor
             * envelope with an empty initial batch, since there are no changes
             * to report at the moment the change stream is created. Therefore,
             * we expect a getMore to be issued when we first advance the change
             * stream with next(). */
            'getMore',
            /* Since socketTimeoutMS is less than maxAwaitTimeMS, the previous
             * getMore command encounters a client socket timeout and leaves the
             * cursor open on the server. ChangeStream should catch this error
             * and resume by issuing a new aggregate command. */
            'aggregate',
            /* When ChangeStream resumes, it overwrites its original cursor with
             * the new cursor resulting from the last aggregate command. This
             * removes the last reference to the old cursor, which causes the
             * driver to kill it (via mongoc_cursor_destroy()). */
            'killCursors',
        ];

        $this->assertSame($expectedCommands, $commands);
    }

    public function testResumeBeforeReceivingAnyResultsIncludesPostBatchResumeToken()
    {
        if ( ! $this->isPostBatchResumeTokenSupported()) {
            $this->markTestSkipped('postBatchResumeToken is not supported');
        }

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);

        $events = [];

        (new CommandObserver)->observe(
            function() use ($operation, &$changeStream) {
                $changeStream = $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use (&$events) {
                $events[] = $event;
            }
        );

        $this->assertCount(1, $events);
        $this->assertSame('aggregate', $events[0]['started']->getCommandName());
        $postBatchResumeToken = $this->getPostBatchResumeTokenFromReply($events[0]['succeeded']->getReply());

        $this->assertFalse($changeStream->valid());
        $this->killChangeStreamCursor($changeStream);

        $this->assertNoCommandExecuted(function() use ($changeStream) { $changeStream->rewind(); });

        $events = [];

        (new CommandObserver)->observe(
            function() use ($changeStream) {
                $changeStream->next();
            },
            function (array $event) use (&$events) {
                $events[] = $event;
            }
        );

        $this->assertCount(3, $events);

        $this->assertSame('getMore', $events[0]['started']->getCommandName());
        $this->arrayHasKey('failed', $events[0]);

        $this->assertSame('aggregate', $events[1]['started']->getCommandName());
        $this->assertResumeAfter($postBatchResumeToken, $events[1]['started']->getCommand());
        $this->arrayHasKey('succeeded', $events[1]);

        // Original cursor is freed immediately after the change stream resumes
        $this->assertSame('killCursors', $events[2]['started']->getCommandName());
        $this->arrayHasKey('succeeded', $events[2]);

        $this->assertFalse($changeStream->valid());
    }

    private function assertResumeAfter($expectedResumeToken, stdClass $command)
    {
        $this->assertObjectHasAttribute('pipeline', $command);
        $this->assertInternalType('array', $command->pipeline);
        $this->assertArrayHasKey(0, $command->pipeline);
        $this->assertObjectHasAttribute('$changeStream', $command->pipeline[0]);
        $this->assertObjectHasAttribute('resumeAfter', $command->pipeline[0]->{'$changeStream'});
        $this->assertEquals($expectedResumeToken, $command->pipeline[0]->{'$changeStream'}->resumeAfter);
    }

    /**
     * Prose test: "$changeStream stage for ChangeStream against a server >=4.0
     * and <4.0.7 that has not received any results yet MUST include a
     * startAtOperationTime option when resuming a changestream."
     */
    public function testResumeBeforeReceivingAnyResultsIncludesStartAtOperationTime()
    {
        if ( ! $this->isStartAtOperationTimeSupported()) {
            $this->markTestSkipped('startAtOperationTime is not supported');
        }

        if ($this->isPostBatchResumeTokenSupported()) {
            $this->markTestSkipped('postBatchResumeToken takes precedence over startAtOperationTime');
        }

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);

        $events = [];

        (new CommandObserver)->observe(
            function() use ($operation, &$changeStream) {
                $changeStream = $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use (&$events) {
                $events[] = $event;
            }
        );

        $this->assertCount(1, $events);
        $this->assertSame('aggregate', $events[0]['started']->getCommandName());
        $reply = $events[0]['succeeded']->getReply();
        $this->assertObjectHasAttribute('operationTime', $reply);
        $operationTime = $reply->operationTime;
        $this->assertInstanceOf(TimestampInterface::class, $operationTime);

        $this->assertFalse($changeStream->valid());
        $this->killChangeStreamCursor($changeStream);

        $this->assertNoCommandExecuted(function() use ($changeStream) { $changeStream->rewind(); });

        $events = [];

        (new CommandObserver)->observe(
            function() use ($changeStream) {
                $changeStream->next();
            },
            function (array $event) use (&$events) {
                $events[] = $event;
            }
        );

        $this->assertCount(3, $events);

        $this->assertSame('getMore', $events[0]['started']->getCommandName());
        $this->arrayHasKey('failed', $events[0]);

        $this->assertSame('aggregate', $events[1]['started']->getCommandName());
        $this->assertStartAtOperationTime($operationTime, $events[1]['started']->getCommand());
        $this->arrayHasKey('succeeded', $events[1]);

        // Original cursor is freed immediately after the change stream resumes
        $this->assertSame('killCursors', $events[2]['started']->getCommandName());
        $this->arrayHasKey('succeeded', $events[2]);

        $this->assertFalse($changeStream->valid());
    }

    private function assertStartAtOperationTime(TimestampInterface $expectedOperationTime, stdClass $command)
    {
        $this->assertObjectHasAttribute('pipeline', $command);
        $this->assertInternalType('array', $command->pipeline);
        $this->assertArrayHasKey(0, $command->pipeline);
        $this->assertObjectHasAttribute('$changeStream', $command->pipeline[0]);
        $this->assertObjectHasAttribute('startAtOperationTime', $command->pipeline[0]->{'$changeStream'});
        $this->assertEquals($expectedOperationTime, $command->pipeline[0]->{'$changeStream'}->startAtOperationTime);
    }

    public function testRewindMultipleTimesWithResults()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['x' => 1]);
        $this->insertDocument(['x' => 2]);

        $this->assertNoCommandExecuted(function() use ($changeStream) { $changeStream->rewind(); });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        // Subsequent rewind does not change iterator state
        $this->assertNoCommandExecuted(function() use ($changeStream) { $changeStream->rewind(); });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertSame(0, $changeStream->key());
        $this->assertNotNull($changeStream->current());

        /* Rewinding when the iterator is still at its first element is a NOP.
         * Note: PHPLIB-448 may see rewind() throw after any call to next() */
        $this->assertNoCommandExecuted(function() use ($changeStream) { $changeStream->rewind(); });
        $this->assertTrue($changeStream->valid());
        $this->assertSame(0, $changeStream->key());
        $this->assertNotNull($changeStream->current());

        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertSame(1, $changeStream->key());
        $this->assertNotNull($changeStream->current());

        // Rewinding after advancing the iterator is an error
        $this->expectException(LogicException::class);
        $changeStream->rewind();
    }

    public function testRewindMultipleTimesWithNoResults()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertNoCommandExecuted(function() use ($changeStream) { $changeStream->rewind(); });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        // Subsequent rewind does not change iterator state
        $this->assertNoCommandExecuted(function() use ($changeStream) { $changeStream->rewind(); });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        $changeStream->next();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        /* Rewinding when the iterator hasn't advanced to an element is a NOP.
         * Note: PHPLIB-448 may see rewind() throw after any call to next() */
        $this->assertNoCommandExecuted(function() use ($changeStream) { $changeStream->rewind(); });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());
    }

    public function testNoChangeAfterResumeBeforeInsert()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertNoCommandExecuted(function() use ($changeStream) { $changeStream->rewind(); });
        $this->assertFalse($changeStream->valid());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 1, 'x' => 'foo'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 1],
        ];

        $this->assertMatchesDocument($expectedResult, $changeStream->current());

        $this->killChangeStreamCursor($changeStream);

        $changeStream->next();
        $this->assertFalse($changeStream->valid());

        $this->insertDocument(['_id' => 2, 'x' => 'bar']);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 2, 'x' => 'bar'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 2],
        ];

        $this->assertMatchesDocument($expectedResult, $changeStream->current());
    }

    public function testResumeMultipleTimesInSuccession()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        /* Killing the cursor when there are no results will test that neither
         * the initial rewind() nor a resume attempt via next() increment the
         * key. */
        $this->killChangeStreamCursor($changeStream);

        $this->assertNoCommandExecuted(function() use ($changeStream) { $changeStream->rewind(); });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        $changeStream->next();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        // A consecutive resume attempt should still not increment the key
        $this->killChangeStreamCursor($changeStream);

        $changeStream->next();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        /* Insert a document and advance the change stream to ensure we capture
         * a resume token. This is necessary when startAtOperationTime is not
         * supported (i.e. 3.6 server version). */
        $this->insertDocument(['_id' => 1]);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertSame(0, $changeStream->key());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 1],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 1],
        ];

        $this->assertMatchesDocument($expectedResult, $changeStream->current());

        /* Insert another document and kill the cursor. ChangeStream::next()
         * should resume and pick up the last insert. */
        $this->insertDocument(['_id' => 2]);
        $this->killChangeStreamCursor($changeStream);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertSame(1, $changeStream->key());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 2],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 2],
        ];

        $this->assertMatchesDocument($expectedResult, $changeStream->current());

        /* Insert another document and kill the cursor. It is technically
         * permissable to call ChangeStream::rewind() since the previous call to
         * next() will have left the cursor positioned at its first and only
         * result. Assert that rewind() does not execute a getMore nor does it
         * modify the iterator's state.
         *
         * Note: PHPLIB-448 may require rewind() to throw an exception here. */
        $this->insertDocument(['_id' => 3]);
        $this->killChangeStreamCursor($changeStream);

        $this->assertNoCommandExecuted(function() use ($changeStream) { $changeStream->rewind(); });
        $this->assertTrue($changeStream->valid());
        $this->assertSame(1, $changeStream->key());
        $this->assertMatchesDocument($expectedResult, $changeStream->current());

        // ChangeStream::next() should resume and pick up the last insert
        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertSame(2, $changeStream->key());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 3],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 3],
        ];

        $this->assertMatchesDocument($expectedResult, $changeStream->current());

        // Test one final, consecutive resume via ChangeStream::next()
        $this->insertDocument(['_id' => 4]);
        $this->killChangeStreamCursor($changeStream);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertSame(3, $changeStream->key());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 4],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 4],
        ];

        $this->assertMatchesDocument($expectedResult, $changeStream->current());
    }

    public function testKey()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        $this->assertNoCommandExecuted(function() use ($changeStream) { $changeStream->rewind(); });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertSame(0, $changeStream->key());

        $changeStream->next();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        $changeStream->next();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        $this->killChangeStreamCursor($changeStream);

        $changeStream->next();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        $this->insertDocument(['_id' => 2, 'x' => 'bar']);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertSame(1, $changeStream->key());
    }

    public function testNonEmptyPipeline()
    {
        $pipeline = [['$project' => ['foo' => [0]]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['_id' => 1]);

        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());

        $changeStream->next();
        $this->assertTrue($changeStream->valid());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'foo' => [0],
        ];

        $this->assertSameDocument($expectedResult, $changeStream->current());
    }

    /**
     * Prose test: "Ensure that a cursor returned from an aggregate command with
     * a cursor id and an initial empty batch is not closed on the driver side."
     */
    public function testInitialCursorIsNotClosed()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), []);
        $changeStream = $operation->execute($this->getPrimaryServer());

        /* The spec requests that we assert that the cursor returned from the
         * aggregate command is not closed on the driver side. We will verify
         * this by checking that the cursor ID is non-zero and that libmongoc
         * reports the cursor as alive. While the cursor ID is easily accessed
         * through ChangeStream, we'll need to use reflection to access the
         * internal Cursor and call isDead(). */
        $this->assertNotEquals('0', (string) $changeStream->getCursorId());

        $rc = new ReflectionClass(ChangeStream::class);
        $rp = $rc->getProperty('iterator');
        $rp->setAccessible(true);

        $iterator = $rp->getValue($changeStream);

        $this->assertInstanceOf('IteratorIterator', $iterator);

        $cursor = $iterator->getInnerIterator();

        $this->assertInstanceOf(Cursor::class, $cursor);
        $this->assertFalse($cursor->isDead());
    }

    /**
     * Prose test: "ChangeStream will not attempt to resume after encountering
     * error code 11601 (Interrupted), 136 (CappedPositionLost), or 237
     * (CursorKilled) while executing a getMore command."
     *
     * @dataProvider provideNonResumableErrorCodes
     */
    public function testNonResumableErrorCodes($errorCode)
    {
        if (version_compare($this->getServerVersion(), '4.0.0', '<')) {
            $this->markTestSkipped('failCommand is not supported');
        }

        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => ['failCommands' => ['getMore'], 'errorCode' => $errorCode],
        ]);

        $this->insertDocument(['x' => 1]);

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), []);
        $changeStream = $operation->execute($this->getPrimaryServer());
        $changeStream->rewind();

        $this->expectException(ServerException::class);
        $this->expectExceptionCode($errorCode);
        $changeStream->next();
    }

    public function provideNonResumableErrorCodes()
    {
        return [
            [136], // CappedPositionLost
            [237], // CursorKilled
            [11601], // Interrupted
        ];
    }

    /**
     * Prose test: "ChangeStream will throw an exception if the server response
     * is missing the resume token (if wire version is < 8, this is a driver-
     * side error; for 8+, this is a server-side error)"
     */
    public function testResumeTokenNotFoundClientSideError()
    {
        if (version_compare($this->getServerVersion(), '4.1.8', '>=')) {
            $this->markTestSkipped('Server rejects change streams that modify resume token (SERVER-37786)');
        }

        $pipeline =  [['$project' => ['_id' => 0 ]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();

        /* Insert two documents to ensure the client does not ignore the first
         * document's resume token in favor of a postBatchResumeToken */
        $this->insertDocument(['x' => 1]);
        $this->insertDocument(['x' => 2]);

        $this->expectException(ResumeTokenException::class);
        $this->expectExceptionMessage('Resume token not found in change document');
        $changeStream->next();
    }

    /**
     * Prose test: "ChangeStream will throw an exception if the server response
     * is missing the resume token (if wire version is < 8, this is a driver-
     * side error; for 8+, this is a server-side error)"
     */
    public function testResumeTokenNotFoundServerSideError()
    {
        if (version_compare($this->getServerVersion(), '4.1.8', '<')) {
            $this->markTestSkipped('Server does not reject change streams that modify resume token');
        }

        $pipeline =  [['$project' => ['_id' => 0 ]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->insertDocument(['x' => 1]);

        $this->expectException(ServerException::class);
        $changeStream->next();
    }

    /**
     * Prose test: "ChangeStream will throw an exception if the server response
     * is missing the resume token (if wire version is < 8, this is a driver-
     * side error; for 8+, this is a server-side error)"
     */
    public function testResumeTokenInvalidTypeClientSideError()
    {
        if (version_compare($this->getServerVersion(), '4.1.8', '>=')) {
            $this->markTestSkipped('Server rejects change streams that modify resume token (SERVER-37786)');
        }

        $pipeline =  [['$project' => ['_id' => ['$literal' => 'foo']]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();

        /* Insert two documents to ensure the client does not ignore the first
         * document's resume token in favor of a postBatchResumeToken */
        $this->insertDocument(['x' => 1]);
        $this->insertDocument(['x' => 2]);

        $this->expectException(ResumeTokenException::class);
        $this->expectExceptionMessage('Expected resume token to have type "array or object" but found "string"');
        $changeStream->next();
    }

    /**
     * Prose test: "ChangeStream will throw an exception if the server response
     * is missing the resume token (if wire version is < 8, this is a driver-
     * side error; for 8+, this is a server-side error)"
     */
    public function testResumeTokenInvalidTypeServerSideError()
    {
        if (version_compare($this->getServerVersion(), '4.1.8', '<')) {
            $this->markTestSkipped('Server does not reject change streams that modify resume token');
        }

        $pipeline =  [['$project' => ['_id' => ['$literal' => 'foo']]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->insertDocument(['x' => 1]);

        $this->expectException(ServerException::class);
        $changeStream->next();
    }

    public function testMaxAwaitTimeMS()
    {
        /* On average, an acknowledged write takes about 20 ms to appear in a
         * change stream on the server so we'll use a higher maxAwaitTimeMS to
         * ensure we see the write. */
        $maxAwaitTimeMS = 500;

        /* Calculate an approximate pivot to use for time assertions. We will
         * assert that the duration of blocking responses is greater than this
         * value, and vice versa. */
        $pivot = ($maxAwaitTimeMS * 0.001) * 0.9;

        /* Calculate an approximate upper bound to use for time assertions. We
         * will assert that the duration of blocking responses is less than this
         * value. */
        $upperBound = ($maxAwaitTimeMS * 0.001) * 1.5;

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], ['maxAwaitTimeMS' => $maxAwaitTimeMS]);
        $changeStream = $operation->execute($this->getPrimaryServer());

        // Rewinding does not issue a getMore, so we should not expect a delay.
        $startTime = microtime(true);
        $changeStream->rewind();
        $duration = microtime(true) - $startTime;
        $this->assertLessThan($pivot, $duration);

        $this->assertFalse($changeStream->valid());

        /* Advancing again on a change stream will issue a getMore, so we should
         * expect a delay. Expect to wait at least maxAwaitTimeMS, since no new
         * documents will be inserted to wake up the server's query thread. Also
         * ensure we don't wait too long (server default is one second). */
        $startTime = microtime(true);
        $changeStream->next();
        $duration = microtime(true) - $startTime;
        $this->assertGreaterThan($pivot, $duration);
        $this->assertLessThan($upperBound, $duration);

        $this->assertFalse($changeStream->valid());

        $this->insertDocument(['_id' => 1]);

        /* Advancing the change stream again will issue a getMore, but the
         * server should not block since a document has been inserted. */
        $startTime = microtime(true);
        $changeStream->next();
        $duration = microtime(true) - $startTime;
        $this->assertLessThan($pivot, $duration);
        $this->assertTrue($changeStream->valid());
    }

    public function testRewindExtractsResumeTokenAndNextResumes()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);
        $this->insertDocument(['_id' => 2, 'x' => 'bar']);
        $this->insertDocument(['_id' => 3, 'x' => 'baz']);

        /* Obtain a resume token for the first insert. This will allow us to
         * start a change stream from that point and ensure aggregate returns
         * the second insert in its first batch, which in turn will serve as a
         * resume token for rewind() to extract. */
        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());

        $changeStream->next();
        $this->assertTrue($changeStream->valid());

        $options = ['resumeAfter' => $changeStream->current()->_id] + $this->defaultOptions;
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertTrue($changeStream->valid());
        $this->assertSame(0, $changeStream->key());
        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 2, 'x' => 'bar'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 2],
        ];
        $this->assertMatchesDocument($expectedResult, $changeStream->current());

        $this->killChangeStreamCursor($changeStream);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertSame(1, $changeStream->key());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 3, 'x' => 'baz'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 3],
        ];
        $this->assertMatchesDocument($expectedResult, $changeStream->current());
    }

    public function testResumeAfterOption()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);
        $this->insertDocument(['_id' => 2, 'x' => 'bar']);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());

        $resumeToken = $changeStream->current()->_id;

        $options = $this->defaultOptions + ['resumeAfter' => $resumeToken];
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertTrue($changeStream->valid());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 2, 'x' => 'bar'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 2],
        ];

        $this->assertMatchesDocument($expectedResult, $changeStream->current());
    }

    public function testStartAfterOption()
    {
        if (version_compare($this->getServerVersion(), '4.1.1', '<')) {
            $this->markTestSkipped('startAfter is not supported');
        }

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);
        $this->insertDocument(['_id' => 2, 'x' => 'bar']);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());

        $resumeToken = $changeStream->current()->_id;

        $options = $this->defaultOptions + ['startAfter' => $resumeToken];
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertTrue($changeStream->valid());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 2, 'x' => 'bar'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 2],
        ];

        $this->assertMatchesDocument($expectedResult, $changeStream->current());
    }

    /**
     * @dataProvider provideTypeMapOptionsAndExpectedChangeDocument
     */
    public function testTypeMapOption(array $typeMap, $expectedChangeDocument)
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], ['typeMap' => $typeMap] + $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());

        $this->assertMatchesDocument($expectedChangeDocument, $changeStream->current());
    }

    public function provideTypeMapOptionsAndExpectedChangeDocument()
    {
        /* Note: the "_id" and "ns" fields are purposefully omitted because the
         * resume token's value cannot be anticipated and the collection name,
         * which is generated from the test name, is not available in the data
         * provider, respectively. */
        return [
            [
                ['root' => 'array', 'document' => 'array'],
                [
                    'operationType' => 'insert',
                    'fullDocument' => ['_id' => 1, 'x' => 'foo'],
                    'documentKey' => ['_id' => 1],
                ],
            ],
            [
                ['root' => 'object', 'document' => 'array'],
                (object) [
                    'operationType' => 'insert',
                    'fullDocument' => ['_id' => 1, 'x' => 'foo'],
                    'documentKey' => ['_id' => 1],
                ],
            ],
            [
                ['root' => 'array', 'document' => 'stdClass'],
                [
                    'operationType' => 'insert',
                    'fullDocument' => (object) ['_id' => 1, 'x' => 'foo'],
                    'documentKey' => (object) ['_id' => 1],
                ],
            ],
        ];
    }

    public function testNextAdvancesKey()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['x' => 1]);
        $this->insertDocument(['x' => 2]);

        /* Note: we intentionally do not start iteration with rewind() to ensure
         * that next() behaves identically when called without rewind(). */
        $changeStream->next();

        $this->assertSame(0, $changeStream->key());

        $changeStream->next();

        $this->assertSame(1, $changeStream->key());
    }

    public function testResumeTokenNotFoundDoesNotAdvanceKey()
    {
        $pipeline =  [['$project' => ['_id' => 0 ]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['x' => 1]);
        $this->insertDocument(['x' => 2]);
        $this->insertDocument(['x' => 3]);

        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        try {
            $changeStream->next();
            $this->fail('Exception for missing resume token was not thrown');
        } catch (ResumeTokenException $e) {
            /* If a client-side error is thrown (server < 4.1.8), the tailable
             * cursor's position is still valid. This may change once PHPLIB-456
             * is implemented. */
            $expectedValid = true;
            $expectedKey = 0;
        } catch (ServerException $e) {
            /* If a server-side error is thrown (server >= 4.1.8), the tailable
             * cursor's position is not valid. */
            $expectedValid = false;
            $expectedKey = null;
        }

        $this->assertSame($expectedValid, $changeStream->valid());
        $this->assertSame($expectedKey, $changeStream->key());

        try {
            $changeStream->next();
            $this->fail('Exception for missing resume token was not thrown');
        } catch (ResumeTokenException $e) {
            $expectedValid = true;
            $expectedKey = 0;
        } catch (ServerException $e) {
            $expectedValid = false;
            $expectedKey = null;
        }

        $this->assertSame($expectedValid, $changeStream->valid());
        $this->assertSame($expectedKey, $changeStream->key());
    }

    public function testSessionPersistsAfterResume()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);

        $changeStream = null;
        $originalSession = null;
        $sessionAfterResume = [];
        $commands = [];

        /* We want to ensure that the lsid of the initial aggregate matches the
         * lsid of any aggregates after the change stream resumes. After
         * PHPC-1152 is complete, we will ensure that the lsid of the initial
         * aggregate matches the lsid of any subsequent aggregates and getMores.
         */
        (new CommandObserver)->observe(
            function() use ($operation, &$changeStream) {
                $changeStream = $operation->execute($this->getPrimaryServer());
            },
            function(array $event) use (&$originalSession) {
                $command = $event['started']->getCommand();
                if (isset($command->aggregate)) {
                    $originalSession = bin2hex((string) $command->lsid->id);
                }
            }
        );

        $changeStream->rewind();
        $this->killChangeStreamCursor($changeStream);

        (new CommandObserver)->observe(
            function() use (&$changeStream) {
                $changeStream->next();
            },
            function (array $event) use (&$sessionAfterResume, &$commands) {
                $commands[] = $event['started']->getCommandName();
                $sessionAfterResume[] = bin2hex((string) $event['started']->getCommand()->lsid->id);
            }
        );

        $expectedCommands = [
            /* We expect a getMore to be issued because we are calling next(). */
            'getMore',
            /* Since we have killed the cursor, ChangeStream will resume by
             * issuing a new aggregate commmand. */
            'aggregate',
            /* When ChangeStream resumes, it overwrites its original cursor with
             * the new cursor resulting from the last aggregate command. This
             * removes the last reference to the old cursor, which causes the
             * driver to kill it (via mongoc_cursor_destroy()). */
            'killCursors',
        ];

        $this->assertSame($expectedCommands, $commands);

        foreach ($sessionAfterResume as $session) {
            $this->assertEquals($session, $originalSession);
        }
    }

    public function testSessionFreed()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $rc = new ReflectionClass($changeStream);
        $rp = $rc->getProperty('resumeCallable');
        $rp->setAccessible(true);

        $this->assertInternalType('callable', $rp->getValue($changeStream));

        // Invalidate the cursor to verify that resumeCallable is unset when the cursor is exhausted.
        $this->dropCollection();

        $changeStream->next();

        $this->assertNull($rp->getValue($changeStream));
    }

    private function assertNoCommandExecuted(callable $callable)
    {
        $commands = [];

        (new CommandObserver)->observe(
            $callable,
            function(array $event) use (&$commands) {
                $this->fail(sprintf('"%s" command was executed', $event['started']->getCommandName()));
            }
        );

        $this->assertEmpty($commands);
    }

    private function getPostBatchResumeTokenFromReply(stdClass $reply)
    {
        $this->assertObjectHasAttribute('cursor', $reply);
        $this->assertInternalType('object', $reply->cursor);
        $this->assertObjectHasAttribute('postBatchResumeToken', $reply->cursor);
        $this->assertInternalType('object', $reply->cursor->postBatchResumeToken);

        return $reply->cursor->postBatchResumeToken;
    }

    private function insertDocument($document)
    {
        $insertOne = new InsertOne(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            $document,
            ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)]
        );
        $writeResult = $insertOne->execute($this->getPrimaryServer());
        $this->assertEquals(1, $writeResult->getInsertedCount());
    }

    private function isPostBatchResumeTokenSupported()
    {
        return version_compare($this->getServerVersion(), '4.0.7', '>=');
    }

    private function isStartAtOperationTimeSupported()
    {
        return \MongoDB\server_supports_feature($this->getPrimaryServer(), self::$wireVersionForStartAtOperationTime);
    }

    private function killChangeStreamCursor(ChangeStream $changeStream)
    {
        $command = [
            'killCursors' => $this->getCollectionName(),
            'cursors' => [ $changeStream->getCursorId() ],
        ];

        $operation = new DatabaseCommand($this->getDatabaseName(), $command);
        $operation->execute($this->getPrimaryServer());
    }
}
