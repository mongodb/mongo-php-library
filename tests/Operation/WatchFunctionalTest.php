<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Client;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\Watch;

class WatchFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        if (version_compare($this->getFeatureCompatibilityVersion(), '3.6', '<')) {
            $this->markTestSkipped('$changeStream is only supported on FCV 3.6 or higher');
        }
   }

    public function testResume()
    {
        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), []);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertNull($changeStream->current());

        $this->insertDocument(['_id' => 2, 'x' => 'bar']);

        $changeStream->next();
        $expectedResult = (object) ([
                            '_id' => $changeStream->current()->_id,
                            'operationType' => 'insert',
                            'fullDocument' => (object) ['_id' => 2, 'x' => 'bar'],
                            'ns' => (object) ['db' => 'phplib_test', 'coll' => 'WatchFunctionalTest.e68b9f01'],
                            'documentKey' => (object) ['_id' => 2]
                        ]);
        $this->assertEquals($changeStream->current(), $expectedResult);

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStream->getCursorId()]]);
        $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['_id' => 3, 'x' => 'baz']);

        $changeStream->next();
        $expectedResult = (object) ([
                            '_id' => $changeStream->current()->_id,
                            'operationType' => 'insert',
                            'fullDocument' => (object) ['_id' => 3, 'x' => 'baz'],
                            'ns' => (object) ['db' => 'phplib_test', 'coll' => 'WatchFunctionalTest.e68b9f01'],
                            'documentKey' => (object) ['_id' => 3]
                        ]);
        $this->assertEquals($changeStream->current(), $expectedResult);
   }

    public function testNoChangeAfterResumeBeforeInsert()
    {
        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), []);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $changeStream->rewind();
        $this->assertNull($changeStream->current());

        $this->insertDocument(['_id' => 2, 'x' => 'bar']);

        $changeStream->next();
        $expectedResult = (object) ([
                            '_id' => $changeStream->current()->_id,
                            'operationType' => 'insert',
                            'fullDocument' => (object) ['_id' => 2, 'x' => 'bar'],
                            'ns' => (object) ['db' => 'phplib_test', 'coll' => 'WatchFunctionalTest.4a554985'],
                            'documentKey' => (object) ['_id' => 2]
                        ]);
        $this->assertEquals($changeStream->current(), $expectedResult);

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStream->getCursorId()]]);
        $operation->execute($this->getPrimaryServer());

        $changeStream->next();
        $this->assertNull($changeStream->current());

        $this->insertDocument(['_id' => 3, 'x' => 'baz']);

        $changeStream->next();
        $expectedResult = (object) ([
                            '_id' => $changeStream->current()->_id,
                            'operationType' => 'insert',
                            'fullDocument' => (object) ['_id' => 3, 'x' => 'baz'],
                            'ns' => (object) ['db' => 'phplib_test', 'coll' => 'WatchFunctionalTest.4a554985'],
                            'documentKey' => (object) ['_id' => 3]
                        ]);
        $this->assertEquals($changeStream->current(), $expectedResult);
    }

    public function testResumeAfterKillThenNoOperations()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), []);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStream->getCursorId()]]);
        $operation->execute($this->getPrimaryServer());

        $changeStream->next();
        $this->assertNull($changeStream->current());
    }

    public function testResumeAfterKillThenOperation()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), []);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStream->getCursorId()]]);
        $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $changeStream->next();
        $this->assertNull($changeStream->current());
    }

    public function testKey()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), []);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertNull($changeStream->key());

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $changeStream->next();
        $this->assertSame(1, $changeStream->key());

        $changeStream->next();
        $this->assertNull($changeStream->key());
        $changeStream->next();
        $this->assertNull($changeStream->key());

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStream->getCursorId()]]);
        $operation->execute($this->getPrimaryServer());

        $changeStream->next();
        $this->assertNull($changeStream->key());

        $this->insertDocument(['_id' => 2, 'x' => 'bar']);

        $changeStream->next();
        $this->assertSame(2, $changeStream->key());
    }

    public function testNonEmptyPipeline()
    {
        $pipeline = [['$project' => ['foo' => [0]]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['_id' => 1]);

        $changeStream->next();
        $expectedResult = (object) ([
                            '_id' => $changeStream->current()->_id,
                            'foo' => [0]
                        ]);
        $this->assertEquals($changeStream->current(), $expectedResult);
    }

    public function testCursorWithEmptyBatchNotClosed()
    {
        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), []);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->assertNotNull($changeStream);
    }

    /**
     * @expectedException MongoDB\Exception\ResumeTokenException
     */
    public function testFailureAfterResumeTokenRemoved()
    {
        $pipeline =  [['$project' => ['_id' => 0 ]]];

        $operation = new Watch($this->manager, $this->getDatabaseName(), $this->getCollectionName(), $pipeline);
        $changeStream = $operation->execute($this->getPrimaryServer());

        $this->insertDocument(['x' => 1]);

        $changeStream->next();
    }

    public function testConnectionException()
    {
        $client = new Client($this->getUri(), ['socketTimeoutMS' => 1005], []);
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());

        $changeStream = $collection->watch();
        $changeStream->next();

        $this->insertDocument(['_id' => 1, 'x' => 'foo']);

        $changeStream->next();
        $expectedResult = (object) ([
                            '_id' => $changeStream->current()->_id,
                            'operationType' => 'insert',
                            'fullDocument' => (object) ['_id' => 1, 'x' => 'foo'],
                            'ns' => (object) ['db' => 'phplib_test', 'coll' => 'WatchFunctionalTest.226d95f1'],
                            'documentKey' => (object) ['_id' => 1]
                        ]);
        $this->assertEquals($changeStream->current(), $expectedResult);
    }

    public function testMaxAwaitTimeMS()
    {
        /* On average, an acknowledged write takes about 20 ms to appear in a
         * change stream on the server so we'll use a higher maxAwaitTimeMS to
         * ensure we see the write. */
        $maxAwaitTimeMS = 100;

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
        $this->assertGreaterThanOrEqual($maxAwaitTimeMS * 0.001, $duration);
        $this->assertLessThan(0.5, $duration);

        $this->assertFalse($changeStream->valid());

        /* Advancing again on a change stream will issue a getMore, so we should
         * expect a delay again. */
        $startTime = microtime(true);
        $changeStream->next();
        $duration = microtime(true) - $startTime;
        $this->assertGreaterThanOrEqual($maxAwaitTimeMS * 0.001, $duration);
        $this->assertLessThan(0.5, $duration);

        $this->assertFalse($changeStream->valid());

        /* After inserting a document, the change stream will not issue a
         * getMore so we should not expect a delay. */
        $this->insertDocument(['_id' => 1]);

        $startTime = microtime(true);
        $changeStream->next();
        $duration = microtime(true) - $startTime;
        $this->assertLessThan($maxAwaitTimeMS * 0.001, $duration);
        $this->assertTrue($changeStream->valid());
    }

    private function insertDocument($document)
    {
        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), $document);
        $writeResult = $insertOne->execute($this->getPrimaryServer());
        $this->assertEquals(1, $writeResult->getInsertedCount());
    }
}
