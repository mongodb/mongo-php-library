<?php

namespace MongoDB\Tests\Operation;

use MongoDB\ChangeStream;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use MongoDB\Exception\ResumeTokenException;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Operation\DropCollection;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\Watch;
use MongoDB\Tests\CommandObserver;
use stdClass;
use ReflectionClass;

class WatchFunctionalTest extends FunctionalTestCase
{
    private $defaultOptions = ['maxAwaitTimeMS' => 500];

    public function setUp()
    {
        parent::setUp();

        if ($this->getPrimaryServer()->getType() === Server::TYPE_STANDALONE) {
            $this->markTestSkipped('$changeStream is not supported on standalone servers');
        }

        if (version_compare($this->getFeatureCompatibilityVersion(), '3.6', '<')) {
            $this->markTestSkipped('$changeStream is only supported on FCV 3.6 or higher');
        }
    }

    public function testNextResumesAfterCursorNotFound()
    {
        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertNull($changeStream->current());

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

        $this->assertSameDocument($expectedResult, $changeStream->current());

        $this->killChangeStreamCursor($changeStream);

        $this->insertDocument(['_id' => 3, 'x' => 'baz']);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 3, 'x' => 'baz'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 3]
        ];

        $this->assertSameDocument($expectedResult, $changeStream->current());
    }

    public function testNextResumesAfterConnectionException()
    {
        /* In order to trigger a dropped connection, we'll use a new client with
         * a socket timeout that is less than the change stream's maxAwaitTimeMS
         * option. */
        $manager = new Manager($this->getUri(), ['socketTimeoutMS' => 50]);
        $primaryServer = $manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        $operation = new Watch($manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($primaryServer);

        /* Note: we intentionally do not start iteration with rewind() to ensure
         * that we test resume functionality within next(). */

        $commands = [];

        try {
            (new CommandObserver)->observe(
                function() use ($changeStream) {
                    $changeStream->next();
                },
                function(stdClass $command) use (&$commands) {
                    $commands[] = key((array) $command);
                }
            );
            $this->fail('ConnectionTimeoutException was not thrown');
        } catch (ConnectionTimeoutException $e) {}

        $expectedCommands = [
            /* The initial aggregate command for change streams returns a cursor
             * envelope with an empty initial batch, since there are no changes
             * to report at the moment the change stream is created. Therefore,
             * we expect a getMore to be issued when we first advance the change
             * stream (with either rewind() or next()). */
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
            /* Finally, ChangeStream will rewind the new cursor as the last step
             * of the resume process. This results in one last getMore. */
            'getMore',
        ];

        $this->assertSame($expectedCommands, $commands);
    }

    public function testRewindResumesAfterConnectionException()
    {
        /* In order to trigger a dropped connection, we'll use a new client with
         * a socket timeout that is less than the change stream's maxAwaitTimeMS
         * option. */
        $manager = new Manager($this->getUri(), ['socketTimeoutMS' => 50]);
        $primaryServer = $manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        $operation = new Watch($manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($primaryServer);

        $commands = [];

        try {
            (new CommandObserver)->observe(
                function() use ($changeStream) {
                    $changeStream->rewind();
                },
                function(stdClass $command) use (&$commands) {
                    $commands[] = key((array) $command);
                }
            );
            $this->fail('ConnectionTimeoutException was not thrown');
        } catch (ConnectionTimeoutException $e) {}

        $expectedCommands = [
            /* The initial aggregate command for change streams returns a cursor
             * envelope with an empty initial batch, since there are no changes
             * to report at the moment the change stream is created. Therefore,
             * we expect a getMore to be issued when we first advance the change
             * stream (with either rewind() or next()). */
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
            /* Finally, ChangeStream will rewind the new cursor as the last step
             * of the resume process. This results in one last getMore. */
            'getMore',
        ];

        $this->assertSame($expectedCommands, $commands);
    }

    public function testNoChangeAfterResumeBeforeInsert()
    {
        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertNull($changeStream->current());

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

        $this->assertSameDocument($expectedResult, $changeStream->current());

        $this->killChangeStreamCursor($changeStream);

        $changeStream->next();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->current());

        $this->insertDocument(['_id' => 3, 'x' => 'baz']);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 3, 'x' => 'baz'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 3],
        ];

        $this->assertSameDocument($expectedResult, $changeStream->current());
    }

    public function testKey()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->key());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $changeStream->rewind();
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
        $this->assertTrue($changeStream->valid());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'foo' => [0],
        ];

        $this->assertSameDocument($expectedResult, $changeStream->current());
    }

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

        $rc = new ReflectionClass('MongoDB\ChangeStream');
        $rp = $rc->getProperty('csIt');
        $rp->setAccessible(true);

        $iterator = $rp->getValue($changeStream);

        $this->assertInstanceOf('IteratorIterator', $iterator);

        $cursor = $iterator->getInnerIterator();

        $this->assertInstanceOf('MongoDB\Driver\Cursor', $cursor);
        $this->assertFalse($cursor->isDead());
    }

    /**
     * @expectedException MongoDB\Exception\ResumeTokenException
     * @expectedExceptionMessage Resume token not found in change document
     */
    public function testNextResumeTokenNotFound()
    {
        $pipeline =  [['$project' => ['_id' => 0 ]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        /* Note: we intentionally do not start iteration with rewind() to ensure
         * that we test extraction functionality within next(). */
        $this->insertDocument(['x' => 1]);

        $changeStream->next();
    }

    /**
     * @expectedException MongoDB\Exception\ResumeTokenException
     * @expectedExceptionMessage Resume token not found in change document
     */
    public function testRewindResumeTokenNotFound()
    {
        $pipeline =  [['$project' => ['_id' => 0 ]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['x' => 1]);

        $changeStream->rewind();
    }

    /**
     * @expectedException MongoDB\Exception\ResumeTokenException
     * @expectedExceptionMessage Expected resume token to have type "array or object" but found "string"
     */
    public function testNextResumeTokenInvalidType()
    {
        $pipeline =  [['$project' => ['_id' => ['$literal' => 'foo']]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        /* Note: we intentionally do not start iteration with rewind() to ensure
         * that we test extraction functionality within next(). */
        $this->insertDocument(['x' => 1]);

        $changeStream->next();
    }

    /**
     * @expectedException MongoDB\Exception\ResumeTokenException
     * @expectedExceptionMessage Expected resume token to have type "array or object" but found "string"
     */
    public function testRewindResumeTokenInvalidType()
    {
        $pipeline =  [['$project' => ['_id' => ['$literal' => 'foo']]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['x' => 1]);

        $changeStream->rewind();
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

        /* The initial change stream is empty so we should expect a delay when
         * we call rewind, since it issues a getMore. Expect to wait at least
         * maxAwaitTimeMS, since no new documents should be inserted to wake up
         * the server's query thread. Also ensure we don't wait too long (server
         * default is one second). */
        $startTime = microtime(true);
        $changeStream->rewind();
        $duration = microtime(true) - $startTime;
        $this->assertGreaterThan($pivot, $duration);
        $this->assertLessThan($upperBound, $duration);

        $this->assertFalse($changeStream->valid());

        /* Advancing again on a change stream will issue a getMore, so we should
         * expect a delay again. */
        $startTime = microtime(true);
        $changeStream->next();
        $duration = microtime(true) - $startTime;
        $this->assertGreaterThan($pivot, $duration);
        $this->assertLessThan($upperBound, $duration);

        $this->assertFalse($changeStream->valid());

        /* After inserting a document, the change stream will not issue a
         * getMore so we should not expect a delay. */
        $this->insertDocument(['_id' => 1]);

        $startTime = microtime(true);
        $changeStream->next();
        $duration = microtime(true) - $startTime;
        $this->assertLessThan($pivot, $duration);
        $this->assertTrue($changeStream->valid());
    }

    public function testRewindResumesAfterCursorNotFound()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->killChangeStreamCursor($changeStream);

        $changeStream->rewind();
        $this->assertFalse($changeStream->valid());
        $this->assertNull($changeStream->current());
    }

    public function testRewindExtractsResumeTokenAndNextResumes()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);
        $this->insertDocument(['_id' => 2, 'x' => 'bar']);

        $changeStream->rewind();
        $this->assertTrue($changeStream->valid());
        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 1, 'x' => 'foo'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 1],
        ];
        $this->assertSameDocument($expectedResult, $changeStream->current());

        $this->killChangeStreamCursor($changeStream);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());

        $expectedResult = [
            '_id' => $changeStream->current()->_id,
            'operationType' => 'insert',
            'fullDocument' => ['_id' => 2, 'x' => 'bar'],
            'ns' => ['db' => $this->getDatabaseName(), 'coll' => $this->getCollectionName()],
            'documentKey' => ['_id' => 2],
        ];
        $this->assertSameDocument($expectedResult, $changeStream->current());
    }

    /**
     * @dataProvider provideTypeMapOptionsAndExpectedChangeDocument
     */
    public function testTypeMapOption(array $typeMap, $expectedChangeDocument)
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], ['typeMap' => $typeMap] + $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertNull($changeStream->current());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $changeDocument = $changeStream->current();

        // Unset the resume token and namespace, which are intentionally omitted
        if (is_array($changeDocument)) {
            unset($changeDocument['_id'], $changeDocument['ns']);
        } else {
            unset($changeDocument->_id, $changeDocument->ns);
        }

        $this->assertEquals($expectedChangeDocument, $changeDocument);
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

        $changeStream->next();

        $this->assertSame(0, $changeStream->key());

        $changeStream->next();

        $this->assertSame(1, $changeStream->key());
    }

    public function testResumeTokenNotFoundAdvancesKey()
    {
        $pipeline =  [['$project' => ['_id' => 0 ]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline, $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        /* Note: we intentionally do not start iteration with rewind() to ensure
         * that we test extraction functionality within next(). */
        $this->insertDocument(['x' => 1]);
        $this->insertDocument(['x' => 2]);
        $this->insertDocument(['x' => 3]);

        try {
            $changeStream->rewind();
            $this->fail('ResumeTokenException was not thrown');
        } catch (ResumeTokenException $e) {}

        $this->assertSame(0, $changeStream->key());

        try {
            $changeStream->next();
            $this->fail('ResumeTokenException was not thrown');
        } catch (ResumeTokenException $e) {}

        $this->assertSame(1, $changeStream->key());

        try {
            $changeStream->next();
            $this->fail('ResumeTokenException was not thrown');
        } catch (ResumeTokenException $e) {}

        $this->assertSame(2, $changeStream->key());
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
            function($changeStream) use (&$originalSession) {
                if (isset($changeStream->aggregate)) {
                    $originalSession = bin2hex((string) $changeStream->lsid->id);
                }
            }
        );

        $changeStream->rewind();
        $this->killChangeStreamCursor($changeStream);

        (new CommandObserver)->observe(
            function() use (&$changeStream) {
                $changeStream->next();
            },
            function ($changeStream) use (&$sessionAfterResume, &$commands) {
                $commands[] = key((array) $changeStream);
                $sessionAfterResume[] = bin2hex((string) $changeStream->lsid->id);
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
            /* Finally, ChangeStream will rewind the new cursor as the last step
             * of the resume process. This results in one last getMore. */
            'getMore',
        ];

        $this->assertSame($expectedCommands, $commands);

        foreach ($sessionAfterResume as $session) {
            $this->assertEquals($session, $originalSession);
        }
    }

    public function testSessionFreed()
    {
        $operation = new CreateCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($this->getPrimaryServer());

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), [], $this->defaultOptions);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $rc = new ReflectionClass($changeStream);
        $rp = $rc->getProperty('resumeCallable');
        $rp->setAccessible(true);

        $this->assertNotNull($rp->getValue($changeStream));

        // Invalidate the cursor to verify that resumeCallable is unset when the cursor is exhausted.
        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($this->getPrimaryServer());

        $changeStream->next();

        $this->assertNull($rp->getValue($changeStream));
    }

    private function insertDocument($document)
    {
        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), $document);
        $writeResult = $insertOne->execute($this->getPrimaryServer());
        $this->assertEquals(1, $writeResult->getInsertedCount());
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
