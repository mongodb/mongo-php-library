<?php

namespace MongoDB\Tests\Operation;

use Closure;
use Iterator;
use MongoDB\BSON\TimestampInterface;
use MongoDB\ChangeStream;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use MongoDB\Driver\Exception\LogicException;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\ResumeTokenException;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\Watch;
use MongoDB\Tests\CommandObserver;
use PHPUnit\Framework\ExpectationFailedException;
use ReflectionClass;
use stdClass;

use function array_diff_key;
use function array_map;
use function assert;
use function bin2hex;
use function microtime;
use function MongoDB\server_supports_feature;
use function sprintf;
use function version_compare;

/**
 * @group matrix-testing-exclude-server-4.2-driver-4.0-topology-sharded_cluster
 * @group matrix-testing-exclude-server-4.4-driver-4.0-topology-sharded_cluster
 * @group matrix-testing-exclude-server-5.0-driver-4.0-topology-sharded_cluster
 */
class WatchFunctionalTest extends FunctionalTestCase
{
    public const INTERRUPTED = 11601;
    public const NOT_PRIMARY = 10107;

    /** @var integer */
    private static $wireVersionForStartAtOperationTime = 7;

    /** @var array */
    private $defaultOptions = ['maxAwaitTimeMS' => 500];

    public function setUp(): void
    {
        parent::setUp();

        $this->skipIfChangeStreamIsNotSupported();
        $this->createCollection();
    }

    /**
     * Prose test 1: "ChangeStream must continuously track the last seen
     * resumeToken"
     */
    public function testGetResumeToken(): void
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

        $this->advanceCursorUntilValid($changeStream);
        $this->assertSameDocument($changeStream->current()->_id, $changeStream->getResumeToken());

        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertSameDocument($changeStream->current()->_id, $changeStream->getResumeToken());

        $this->insertDocument(['x' => 3]);

        $this->advanceCursorUntilValid($changeStream);
        $this->assertSameDocument($changeStream->current()->_id, $changeStream->getResumeToken());
    }

    /**
     * Prose test 1: "ChangeStream must continuously track the last seen
     * resumeToken"
     *
     * Prose test 11:
     * For a ChangeStream under these conditions:
     *  - Running against a server >=4.0.7.
     *  - The batch is empty or has been iterated to the last document.
     * Expected result: getResumeToken must return the postBatchResumeToken from
     * the current command response.
     *
     * Prose test 13:
     * For a ChangeStream under these conditions:
     *  - The batch is not empty.
     *  - The batch has been iterated up to but not including the last element.
     * Expected result: getResumeToken must return the _id of the previous
     * document returned.
     */
    public function testGetResumeTokenWithPostBatchResumeToken(): void
    {
        if (! $this->isPostBatchResumeTokenSupported()) {
            $this->markTestSkipped('postBatchResumeToken is not supported');
        }

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);

        $events = [];

        (new CommandObserver())->observe(
            function () use ($operation, &$changeStream): void {
                $changeStream = $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use (&$events): void {
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

        $lastEvent = null;

        (new CommandObserver())->observe(
            function () use ($changeStream): void {
                $this->advanceCursorUntilValid($changeStream);
            },
            function (array $event) use (&$lastEvent): void {
                $lastEvent = $event;
            }
        );

        $this->assertNotNull($lastEvent);
        $this->assertSame('getMore', $lastEvent['started']->getCommandName());
        $postBatchResumeToken = $this->getPostBatchResumeTokenFromReply($lastEvent['succeeded']->getReply());

        $this->assertSameDocument($changeStream->current()->_id, $changeStream->getResumeToken());

        $changeStream->next();
        $this->assertSameDocument($postBatchResumeToken, $changeStream->getResumeToken());
    }

    public function testNextResumesAfterConnectionException(): void
    {
        $this->skipIfIsShardedCluster('initial aggregate command times out due to socketTimeoutMS');

        /* In order to trigger a dropped connection, we'll use a new client with
         * a socket timeout that is less than the change stream's maxAwaitTimeMS
         * option. */
        $manager = static::createTestManager(null, ['socketTimeoutMS' => 50]);
        $primaryServer = $manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        $operation = new Watch($manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($primaryServer);
        $changeStream->rewind();

        $commands = [];

        (new CommandObserver())->observe(
            function () use ($changeStream): void {
                $changeStream->next();
            },
            function (array $event) use (&$commands): void {
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

    public function testResumeBeforeReceivingAnyResultsIncludesPostBatchResumeToken(): void
    {
        if (! $this->isPostBatchResumeTokenSupported()) {
            $this->markTestSkipped('postBatchResumeToken is not supported');
        }

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);

        $events = [];

        (new CommandObserver())->observe(
            function () use ($operation, &$changeStream): void {
                $changeStream = $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use (&$events): void {
                $events[] = $event;
            }
        );

        $this->assertCount(1, $events);
        $this->assertSame('aggregate', $events[0]['started']->getCommandName());
        $postBatchResumeToken = $this->getPostBatchResumeTokenFromReply($events[0]['succeeded']->getReply());

        $this->assertFalse($changeStream->valid());
        $this->forceChangeStreamResume();

        $this->assertNoCommandExecuted(function () use ($changeStream): void {
            $changeStream->rewind();
        });

        $events = [];

        (new CommandObserver())->observe(
            function () use ($changeStream): void {
                $changeStream->next();
            },
            function (array $event) use (&$events): void {
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

    private function assertResumeAfter($expectedResumeToken, stdClass $command): void
    {
        $this->assertObjectHasAttribute('pipeline', $command);
        $this->assertIsArray($command->pipeline);
        $this->assertArrayHasKey(0, $command->pipeline);
        $this->assertObjectHasAttribute('$changeStream', $command->pipeline[0]);
        $this->assertObjectHasAttribute('resumeAfter', $command->pipeline[0]->{'$changeStream'});
        $this->assertEquals($expectedResumeToken, $command->pipeline[0]->{'$changeStream'}->resumeAfter);
    }

    /**
     * Prose test 9: "$changeStream stage for ChangeStream against a server
     * >=4.0 and <4.0.7 that has not received any results yet MUST include a
     * startAtOperationTime option when resuming a changestream."
     */
    public function testResumeBeforeReceivingAnyResultsIncludesStartAtOperationTime(): void
    {
        if (! $this->isStartAtOperationTimeSupported()) {
            $this->markTestSkipped('startAtOperationTime is not supported');
        }

        if ($this->isPostBatchResumeTokenSupported()) {
            $this->markTestSkipped('postBatchResumeToken takes precedence over startAtOperationTime');
        }

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);

        $events = [];

        (new CommandObserver())->observe(
            function () use ($operation, &$changeStream): void {
                $changeStream = $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use (&$events): void {
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
        $this->forceChangeStreamResume();

        $this->assertNoCommandExecuted(function () use ($changeStream): void {
            $changeStream->rewind();
        });

        $events = [];

        (new CommandObserver())->observe(
            function () use ($changeStream): void {
                $changeStream->next();
            },
            function (array $event) use (&$events): void {
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

    private function assertStartAtOperationTime(TimestampInterface $expectedOperationTime, stdClass $command): void
    {
        $this->assertObjectHasAttribute('pipeline', $command);
        $this->assertIsArray($command->pipeline);
        $this->assertArrayHasKey(0, $command->pipeline);
        $this->assertObjectHasAttribute('$changeStream', $command->pipeline[0]);
        $this->assertObjectHasAttribute('startAtOperationTime', $command->pipeline[0]->{'$changeStream'});
        $this->assertEquals($expectedOperationTime, $command->pipeline[0]->{'$changeStream'}->startAtOperationTime);
    }

    public function testRewindMultipleTimesWithResults(): void
    {
        $this->skipIfIsShardedCluster('Cursor needs to be advanced multiple times and can\'t be rewound afterwards.');

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['x' => 1]);
        $this->insertDocument(['x' => 2]);

        $this->assertNoCommandExecuted(function () use ($changeStream): void {
            $changeStream->rewind();
        });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        // Subsequent rewind does not change iterator state
        $this->assertNoCommandExecuted(function () use ($changeStream): void {
            $changeStream->rewind();
        });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertSame(0, $changeStream->key());
        $this->assertNotNull($changeStream->current());

        /* Rewinding when the iterator is still at its first element is a NOP.
         * Note: PHPLIB-448 may see rewind() throw after any call to next() */
        $this->assertNoCommandExecuted(function () use ($changeStream): void {
            $changeStream->rewind();
        });
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

    public function testRewindMultipleTimesWithNoResults(): void
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertNoCommandExecuted(function () use ($changeStream): void {
            $changeStream->rewind();
        });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        // Subsequent rewind does not change iterator state
        $this->assertNoCommandExecuted(function () use ($changeStream): void {
            $changeStream->rewind();
        });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        $changeStream->next();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        /* Rewinding when the iterator hasn't advanced to an element is a NOP.
         * Note: PHPLIB-448 may see rewind() throw after any call to next() */
        $this->assertNoCommandExecuted(function () use ($changeStream): void {
            $changeStream->rewind();
        });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());
    }

    public function testNoChangeAfterResumeBeforeInsert(): void
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertNoCommandExecuted(function () use ($changeStream): void {
            $changeStream->rewind();
        });
        $this->assertFalse($changeStream->valid());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $this->advanceCursorUntilValid($changeStream);

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 1, 'x' => 'foo'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 1],
        ];

        $this->assertMatchesDocument($expectedResult, $changeStream->current());

        $this->forceChangeStreamResume();

        $changeStream->next();
        $this->assertFalse($changeStream->valid());

        $this->insertDocument(['_id' => 2, 'x' => 'bar']);

        $this->advanceCursorUntilValid($changeStream);

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 2, 'x' => 'bar'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 2],
        ];

        $this->assertMatchesDocument($expectedResult, $changeStream->current());
    }

    public function testResumeMultipleTimesInSuccession(): void
    {
        $this->skipIfIsShardedCluster('getMore may return empty response before periodicNoopIntervalSecs on sharded clusters.');

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        /* Forcing a resume when there are no results will test that neither
         * the initial rewind() nor a resume attempt via next() increment the
         * key. */
        $this->forceChangeStreamResume();

        $this->assertNoCommandExecuted(function () use ($changeStream): void {
            $changeStream->rewind();
        });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        $changeStream->next();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
        $this->assertNull($changeStream->current());

        // A consecutive resume attempt should still not increment the key
        $this->forceChangeStreamResume();

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

        /* Insert another document and force a resume. ChangeStream::next()
         * should resume and pick up the last insert. */
        $this->insertDocument(['_id' => 2]);
        $this->forceChangeStreamResume();

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
        $this->forceChangeStreamResume();

        $this->assertNoCommandExecuted(function () use ($changeStream): void {
            $changeStream->rewind();
        });
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
        $this->forceChangeStreamResume();

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

    public function testKey(): void
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        $this->assertNoCommandExecuted(function () use ($changeStream): void {
            $changeStream->rewind();
        });
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $this->advanceCursorUntilValid($changeStream);
        $this->assertSame(0, $changeStream->key());

        $changeStream->next();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        $changeStream->next();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        $this->forceChangeStreamResume();

        $changeStream->next();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        $this->insertDocument(['_id' => 2, 'x' => 'bar']);

        $this->advanceCursorUntilValid($changeStream);
        $this->assertSame(1, $changeStream->key());
    }

    public function testNonEmptyPipeline(): void
    {
        $pipeline = [['$project' => ['foo' => [0]]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['_id' => 1]);

        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());

        $this->advanceCursorUntilValid($changeStream);

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'foo' => [0],
        ];

        $this->assertSameDocument($expectedResult, $changeStream->current());
    }

    /**
     * Prose test 7: "Ensure that a cursor returned from an aggregate command
     * with a cursor id and an initial empty batch is not closed on the driver
     * side."
     */
    public function testInitialCursorIsNotClosed(): void
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
     * Prose test 2: "ChangeStream will throw an exception if the server
     * response is missing the resume token (if wire version is < 8, this is a
     * driver-side error; for 8+, this is a server-side error)"
     */
    public function testResumeTokenNotFoundClientSideError(): void
    {
        if (version_compare($this->getServerVersion(), '4.1.8', '>=')) {
            $this->markTestSkipped('Server rejects change streams that modify resume token (SERVER-37786)');
        }

        $pipeline =  [['$project' => ['_id' => 0]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();

        /* Insert two documents to ensure the client does not ignore the first
         * document's resume token in favor of a postBatchResumeToken */
        $this->insertDocument(['x' => 1]);
        $this->insertDocument(['x' => 2]);

        $this->expectException(ResumeTokenException::class);
        $this->expectExceptionMessage('Resume token not found in change document');
        $this->advanceCursorUntilValid($changeStream);
    }

    /**
     * Prose test 2: "ChangeStream will throw an exception if the server
     * response is missing the resume token (if wire version is < 8, this is a
     * driver-side error; for 8+, this is a server-side error)"
     */
    public function testResumeTokenNotFoundServerSideError(): void
    {
        if (version_compare($this->getServerVersion(), '4.1.8', '<')) {
            $this->markTestSkipped('Server does not reject change streams that modify resume token');
        }

        $pipeline =  [['$project' => ['_id' => 0]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->insertDocument(['x' => 1]);

        $this->expectException(ServerException::class);
        $this->advanceCursorUntilValid($changeStream);
    }

    /**
     * Prose test 2: "ChangeStream will throw an exception if the server
     * response is missing the resume token (if wire version is < 8, this is a
     * driver-side error; for 8+, this is a server-side error)"
     */
    public function testResumeTokenInvalidTypeClientSideError(): void
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
        $this->advanceCursorUntilValid($changeStream);
    }

    /**
     * Prose test 2: "ChangeStream will throw an exception if the server
     * response is missing the resume token (if wire version is < 8, this is a
     * driver-side error; for 8+, this is a server-side error)"
     */
    public function testResumeTokenInvalidTypeServerSideError(): void
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
        $this->advanceCursorUntilValid($changeStream);
    }

    public function testMaxAwaitTimeMS(): void
    {
        /* On average, an acknowledged write takes about 20 ms to appear in a
         * change stream on the server so we'll use a higher maxAwaitTimeMS to
         * ensure we see the write. */
        $maxAwaitTimeMS = 500;

        /* Calculate an approximate pivot to use for time assertions. We will
         * assert that the duration of blocking responses is greater than this
         * value, and vice versa. */
        $pivot = $maxAwaitTimeMS * 0.001 * 0.9;

        /* Calculate an approximate upper bound to use for time assertions. We
         * will assert that the duration of blocking responses is less than this
         * value. */
        $upperBound = $maxAwaitTimeMS * 0.001 * 1.5;

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
         * server should not block since a document has been inserted.
         * For sharded clusters, we have to repeat the getMore iteration until
         * the cursor is valid since the first getMore commands after an insert
         * may not return any data. Only the time of the last getMore command is
         * taken. */
        $attempts = $this->isShardedCluster() ? 5 : 1;
        for ($i = 0; $i < $attempts; $i++) {
            $startTime = microtime(true);
            $changeStream->next();
            $duration = microtime(true) - $startTime;

            if ($changeStream->valid()) {
                break;
            }
        }

        $this->assertTrue($changeStream->valid());

        if (! $this->isShardedCluster()) {
            $this->assertLessThan($pivot, $duration);
        }
    }

    public function testRewindExtractsResumeTokenAndNextResumes(): void
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

        $this->advanceCursorUntilValid($changeStream);

        $resumeToken = $changeStream->current()->_id;
        $options = ['resumeAfter' => $resumeToken] + $this->defaultOptions;
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
        $changeStream = $operation->execute($this->getPrimaryServer());
        $this->assertSameDocument($resumeToken, $changeStream->getResumeToken());

        $changeStream->rewind();

        if ($this->isShardedCluster()) {
            /* aggregate on a sharded cluster may not return any data in the
             * initial batch until periodicNoopIntervalSecs has passed. Thus,
             * advance the change stream until we've received data. */
            $this->advanceCursorUntilValid($changeStream);
        } else {
            $this->assertTrue($changeStream->valid());
        }

        $this->assertSame(0, $changeStream->key());
        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 2, 'x' => 'bar'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 2],
        ];
        $this->assertMatchesDocument($expectedResult, $changeStream->current());

        $this->forceChangeStreamResume();

        $this->advanceCursorUntilValid($changeStream);
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

    public function testResumeAfterOption(): void
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);
        $this->insertDocument(['_id' => 2, 'x' => 'bar']);

        $this->advanceCursorUntilValid($changeStream);

        $resumeToken = $changeStream->current()->_id;

        $options = $this->defaultOptions + ['resumeAfter' => $resumeToken];
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
        $changeStream = $operation->execute($this->getPrimaryServer());
        $this->assertSameDocument($resumeToken, $changeStream->getResumeToken());

        $changeStream->rewind();

        if ($this->isShardedCluster()) {
            /* aggregate on a sharded cluster may not return any data in the
             * initial batch until periodicNoopIntervalSecs has passed. Thus,
             * advance the change stream until we've received data. */
            $this->advanceCursorUntilValid($changeStream);
        } else {
            $this->assertTrue($changeStream->valid());
        }

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 2, 'x' => 'bar'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 2],
        ];

        $this->assertMatchesDocument($expectedResult, $changeStream->current());
    }

    public function testStartAfterOption(): void
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

        $this->advanceCursorUntilValid($changeStream);

        $resumeToken = $changeStream->current()->_id;

        $options = $this->defaultOptions + ['startAfter' => $resumeToken];
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
        $changeStream = $operation->execute($this->getPrimaryServer());
        $this->assertSameDocument($resumeToken, $changeStream->getResumeToken());

        $changeStream->rewind();

        if ($this->isShardedCluster()) {
            /* aggregate on a sharded cluster may not return any data in the
             * initial batch until periodicNoopIntervalSecs has passed. Thus,
             * advance the change stream until we've received data. */
            $this->advanceCursorUntilValid($changeStream);
        } else {
            $this->assertTrue($changeStream->valid());
        }

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
    public function testTypeMapOption(array $typeMap, $expectedChangeDocument): void
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], ['typeMap' => $typeMap] + $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $this->advanceCursorUntilValid($changeStream);

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

    public function testNextAdvancesKey(): void
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['x' => 1]);
        $this->insertDocument(['x' => 2]);

        /* Note: we intentionally do not start iteration with rewind() to ensure
         * that next() behaves identically when called without rewind(). */
        $this->advanceCursorUntilValid($changeStream);

        $this->assertSame(0, $changeStream->key());

        $changeStream->next();

        $this->assertSame(1, $changeStream->key());
    }

    public function testResumeTokenNotFoundDoesNotAdvanceKey(): void
    {
        $pipeline =  [['$project' => ['_id' => 0]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['x' => 1]);
        $this->insertDocument(['x' => 2]);
        $this->insertDocument(['x' => 3]);

        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        try {
            $this->advanceCursorUntilValid($changeStream);
            $this->fail('Exception for missing resume token was not thrown');
        } catch (ResumeTokenException $e) {
            /* On server versions < 4.1.8, a client-side error is thrown. */
        } catch (ServerException $e) {
            /* On server versions >= 4.1.8, the error is thrown server-side. */
        }

        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        try {
            $changeStream->next();
            $this->fail('Exception for missing resume token was not thrown');
        } catch (ResumeTokenException $e) {
        } catch (ServerException $e) {
        }

        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());
    }

    public function testSessionPersistsAfterResume(): void
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
        (new CommandObserver())->observe(
            function () use ($operation, &$changeStream): void {
                $changeStream = $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use (&$originalSession): void {
                $command = $event['started']->getCommand();
                if (isset($command->aggregate)) {
                    $originalSession = bin2hex((string) $command->lsid->id);
                }
            }
        );

        $changeStream->rewind();
        $this->forceChangeStreamResume();

        (new CommandObserver())->observe(
            function () use (&$changeStream): void {
                $changeStream->next();
            },
            function (array $event) use (&$sessionAfterResume, &$commands): void {
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

    public function testSessionFreed(): void
    {
        if ($this->isShardedCluster() && version_compare($this->getServerVersion(), '5.1.0', '>=')) {
            $this->markTestSkipped('mongos still reports non-zero cursor ID for invalidated change stream (SERVER-60764)');
        }

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $rc = new ReflectionClass($changeStream);
        $rp = $rc->getProperty('resumeCallable');
        $rp->setAccessible(true);

        $this->assertIsCallable($rp->getValue($changeStream));

        // Invalidate the cursor to verify that resumeCallable is unset when the cursor is exhausted.
        $this->dropCollection();

        $this->advanceCursorUntilValid($changeStream);

        $this->assertNull($rp->getValue($changeStream));
    }

    /**
     * Prose test 3: "ChangeStream will automatically resume one time on a
     * resumable error (including not primary) with the initial pipeline and
     * options, except for the addition/update of a resumeToken."
     */
    public function testResumeRepeatsOriginalPipelineAndOptions(): void
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);

        $aggregateCommands = [];

        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => [
                'failCommands' => ['getMore'],
                'errorCode' => self::NOT_PRIMARY,
                'errorLabels' => ['ResumableChangeStreamError'],
            ],
        ]);

        (new CommandObserver())->observe(
            function () use ($operation): void {
                $changeStream = $operation->execute($this->getPrimaryServer());

                // The first next will hit the fail point, causing a resume
                $changeStream->next();
                $changeStream->next();
            },
            function (array $event) use (&$aggregateCommands): void {
                $command = $event['started']->getCommand();
                if ($event['started']->getCommandName() !== 'aggregate') {
                    return;
                }

                $aggregateCommands[] = (array) $command;
            }
        );

        $this->assertCount(2, $aggregateCommands);

        $this->assertThat(
            $aggregateCommands[0]['pipeline'][0]->{'$changeStream'},
            $this->logicalNot(
                $this->logicalOr(
                    $this->objectHasAttribute('resumeAfter'),
                    $this->objectHasAttribute('startAfter'),
                    $this->objectHasAttribute('startAtOperationTime')
                )
            )
        );

        $this->assertThat(
            $aggregateCommands[1]['pipeline'][0]->{'$changeStream'},
            $this->logicalOr(
                $this->objectHasAttribute('resumeAfter'),
                $this->objectHasAttribute('startAfter'),
                $this->objectHasAttribute('startAtOperationTime')
            )
        );

        $aggregateCommands = array_map(
            function (array $aggregateCommand) {
                // Remove resume options from the changestream document
                if (isset($aggregateCommand['pipeline'][0]->{'$changeStream'})) {
                    $aggregateCommand['pipeline'][0]->{'$changeStream'} = array_diff_key(
                        (array) $aggregateCommand['pipeline'][0]->{'$changeStream'},
                        ['resumeAfter' => false, 'startAfter' => false, 'startAtOperationTime' => false]
                    );
                }

                // Remove options we don't want to compare between commands
                return array_diff_key($aggregateCommand, ['lsid' => false, '$clusterTime' => false]);
            },
            $aggregateCommands
        );

        // Ensure options in original and resuming aggregate command match
        $this->assertEquals($aggregateCommands[0], $aggregateCommands[1]);
    }

    /**
     * Prose test 4: "ChangeStream will not attempt to resume on any error
     * encountered while executing an aggregate command."
     */
    public function testErrorDuringAggregateCommandDoesNotCauseResume(): void
    {
        if (version_compare($this->getServerVersion(), '4.0.0', '<')) {
            $this->markTestSkipped('failCommand is not supported');
        }

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);

        $commandCount = 0;

        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => ['failCommands' => ['aggregate'], 'errorCode' => self::INTERRUPTED],
        ]);

        $this->expectException(CommandException::class);

        (new CommandObserver())->observe(
            function () use ($operation): void {
                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use (&$commandCount): void {
                $commandCount++;
            }
        );

        $this->assertSame(1, $commandCount);
    }

    /**
     * Prose test 6: "ChangeStream will perform server selection before
     * attempting to resume, using initial readPreference"
     */
    public function testOriginalReadPreferenceIsPreservedOnResume(): void
    {
        if ($this->isShardedCluster()) {
            $this->markTestSkipped('Test does not apply to sharded clusters');
        }

        $readPreference = new ReadPreference('secondary');
        $options = ['readPreference' => $readPreference] + $this->defaultOptions;
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);

        try {
            $secondary = $this->manager->selectServer($readPreference);
        } catch (ConnectionTimeoutException $e) {
            $this->markTestSkipped('Secondary is not available');
        }

        $changeStream = $operation->execute($secondary);
        $previousCursorId = $changeStream->getCursorId();
        $this->forceChangeStreamResume();

        $changeStream->next();
        $this->assertNotSame($previousCursorId, $changeStream->getCursorId());

        $getCursor = Closure::bind(
            function () {
                return $this->iterator->getInnerIterator();
            },
            $changeStream,
            ChangeStream::class
        );
        $cursor = $getCursor();
        assert($cursor instanceof Cursor);
        self::assertTrue($cursor->getServer()->isSecondary());
    }

    /**
     * Prose test 12
     * For a ChangeStream under these conditions:
     * - Running against a server <4.0.7.
     * - The batch is empty or has been iterated to the last document.
     * Expected result:
     * - getResumeToken must return the _id of the last document returned if one exists.
     * - getResumeToken must return resumeAfter from the initial aggregate if the option was specified.
     * - If resumeAfter was not specified, the getResumeToken result must be empty.
     */
    public function testGetResumeTokenReturnsOriginalResumeTokenOnEmptyBatch(): void
    {
        if ($this->isPostBatchResumeTokenSupported()) {
            $this->markTestSkipped('postBatchResumeToken is supported');
        }

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertNull($changeStream->getResumeToken());

        $this->insertDocument(['x' => 1]);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $resumeToken = $changeStream->getResumeToken();
        $this->assertSame($resumeToken, $changeStream->current()->_id);

        $options = ['resumeAfter' => $resumeToken] + $this->defaultOptions;
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertSame($resumeToken, $changeStream->getResumeToken());
    }

    /**
     * Prose test 14
     * For a ChangeStream under these conditions:
     *  - The batch is not empty.
     *  - The batch hasn’t been iterated at all.
     *  - Only the initial aggregate command has been executed.
     * Expected result:
     *  - getResumeToken must return startAfter from the initial aggregate if the option was specified.
     *  - getResumeToken must return resumeAfter from the initial aggregate if the option was specified.
     *  - If neither the startAfter nor resumeAfter options were specified, the getResumeToken result must be empty.
     */
    public function testResumeTokenBehaviour(): void
    {
        if (version_compare($this->getServerVersion(), '4.1.1', '<')) {
            $this->markTestSkipped('Testing resumeAfter and startAfter can only be tested on servers >= 4.1.1');
        }

        $this->skipIfIsShardedCluster('Resume token behaviour can\'t be reliably tested on sharded clusters.');

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);

        $lastOpTime = null;

        $changeStream = null;
        (new CommandObserver())->observe(function () use ($operation, &$changeStream): void {
            $changeStream = $operation->execute($this->getPrimaryServer());
        }, function ($event) use (&$lastOpTime): void {
            $this->assertInstanceOf(CommandSucceededEvent::class, $event['succeeded']);
            $reply = $event['succeeded']->getReply();

            $this->assertObjectHasAttribute('operationTime', $reply);
            $lastOpTime = $reply->operationTime;
        });

        $this->insertDocument(['x' => 1]);

        $this->advanceCursorUntilValid($changeStream);
        $this->assertTrue($changeStream->valid());
        $resumeToken = $changeStream->getResumeToken();

        $this->insertDocument(['x' => 2]);

        // Test startAfter option
        $options = ['startAfter' => $resumeToken] + $this->defaultOptions;
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertEquals($resumeToken, $changeStream->getResumeToken());

        // Test resumeAfter option
        $options = ['resumeAfter' => $resumeToken] + $this->defaultOptions;
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertEquals($resumeToken, $changeStream->getResumeToken());

        // Test without option
        $options = ['startAtOperationTime' => $lastOpTime] + $this->defaultOptions;
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertNull($changeStream->getResumeToken());
    }

    /**
     * Prose test 17: "$changeStream stage for ChangeStream started with
     * startAfter against a server >=4.1.1 that has not received any results yet
     * MUST include a startAfter option and MUST NOT include a resumeAfter
     * option when resuming a change stream."
     */
    public function testResumingChangeStreamWithoutPreviousResultsIncludesStartAfterOption(): void
    {
        if (version_compare($this->getServerVersion(), '4.1.1', '<')) {
            $this->markTestSkipped('Testing resumeAfter and startAfter can only be tested on servers >= 4.1.1');
        }

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['x' => 1]);

        $this->advanceCursorUntilValid($changeStream);
        $this->assertTrue($changeStream->valid());
        $resumeToken = $changeStream->getResumeToken();

        $options = ['startAfter' => $resumeToken] + $this->defaultOptions;
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
        $changeStream = $operation->execute($this->getPrimaryServer());
        $changeStream->rewind();
        $this->forceChangeStreamResume();

        $aggregateCommand = null;

        (new CommandObserver())->observe(
            function () use ($changeStream): void {
                $changeStream->next();
            },
            function (array $event) use (&$aggregateCommand): void {
                if ($event['started']->getCommandName() !== 'aggregate') {
                    return;
                }

                $aggregateCommand = $event['started']->getCommand();
            }
        );

        $this->assertNotNull($aggregateCommand);
        $this->assertObjectNotHasAttribute('resumeAfter', $aggregateCommand->pipeline[0]->{'$changeStream'});
        $this->assertObjectHasAttribute('startAfter', $aggregateCommand->pipeline[0]->{'$changeStream'});
    }

    /**
     * Prose test 18: "$changeStream stage for ChangeStream started with
     * startAfter against a server >=4.1.1 that has received at least one result
     * MUST include a resumeAfter option and MUST NOT include a startAfter
     * option when resuming a change stream."
     */
    public function testResumingChangeStreamWithPreviousResultsIncludesResumeAfterOption(): void
    {
        if (version_compare($this->getServerVersion(), '4.1.1', '<')) {
            $this->markTestSkipped('Testing resumeAfter and startAfter can only be tested on servers >= 4.1.1');
        }

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['x' => 1]);

        $this->advanceCursorUntilValid($changeStream);
        $resumeToken = $changeStream->getResumeToken();

        $options = ['startAfter' => $resumeToken] + $this->defaultOptions;
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $options);
        $changeStream = $operation->execute($this->getPrimaryServer());
        $changeStream->rewind();

        $this->insertDocument(['x' => 2]);
        $this->advanceCursorUntilValid($changeStream);
        $this->assertTrue($changeStream->valid());

        $this->forceChangeStreamResume();

        $aggregateCommand = null;

        (new CommandObserver())->observe(
            function () use ($changeStream): void {
                $changeStream->next();
            },
            function (array $event) use (&$aggregateCommand): void {
                if ($event['started']->getCommandName() !== 'aggregate') {
                    return;
                }

                $aggregateCommand = $event['started']->getCommand();
            }
        );

        $this->assertNotNull($aggregateCommand);
        $this->assertObjectNotHasAttribute('startAfter', $aggregateCommand->pipeline[0]->{'$changeStream'});
        $this->assertObjectHasAttribute('resumeAfter', $aggregateCommand->pipeline[0]->{'$changeStream'});
    }

    private function assertNoCommandExecuted(callable $callable): void
    {
        $commands = [];

        (new CommandObserver())->observe(
            $callable,
            function (array $event) use (&$commands): void {
                $this->fail(sprintf('"%s" command was executed', $event['started']->getCommandName()));
            }
        );

        $this->assertEmpty($commands);
    }

    private function forceChangeStreamResume(): void
    {
        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => [
                'failCommands' => ['getMore'],
                'errorCode' => self::NOT_PRIMARY,
                'errorLabels' => ['ResumableChangeStreamError'],
            ],
        ]);
    }

    private function getPostBatchResumeTokenFromReply(stdClass $reply)
    {
        $this->assertObjectHasAttribute('cursor', $reply);
        $this->assertIsObject($reply->cursor);
        $this->assertObjectHasAttribute('postBatchResumeToken', $reply->cursor);
        $this->assertIsObject($reply->cursor->postBatchResumeToken);

        return $reply->cursor->postBatchResumeToken;
    }

    private function insertDocument($document): void
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
        return server_supports_feature($this->getPrimaryServer(), self::$wireVersionForStartAtOperationTime);
    }

    private function advanceCursorUntilValid(Iterator $iterator, $limitOnShardedClusters = 10): void
    {
        if (! $this->isShardedCluster()) {
            $iterator->next();
            $this->assertTrue($iterator->valid());

            return;
        }

        for ($i = 0; $i < $limitOnShardedClusters; $i++) {
            $iterator->next();
            if ($iterator->valid()) {
                return;
            }
        }

        throw new ExpectationFailedException(sprintf('Expected cursor to return an element but none was found after %d attempts.', $limitOnShardedClusters));
    }

    private function skipIfIsShardedCluster($message): void
    {
        if (! $this->isShardedCluster()) {
            return;
        }

        $this->markTestSkipped(sprintf('Test does not apply on sharded clusters: %s', $message));
    }
}
