<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Operation\DatabaseCommand;

class ChangeStreamFunctionalTest extends FunctionalTestCase
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
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $result = $this->collection->insertOne(['x' => 1]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult = $this->collection->watch();
        $changeStreamResult->rewind();
        $this->assertNull($changeStreamResult->current());

        $result = $this->collection->insertOne(['x' => 2]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
        $expectedResult = (object) ([
                            '_id' => $changeStreamResult->current()->_id,
                            'operationType' => 'insert',
                            'fullDocument' => (object) ['_id' => $result->getInsertedId(), 'x' => 2],
                            'ns' => (object) ['db' => 'phplib_test', 'coll' => 'ChangeStreamFunctionalTest.e68b9f01'],
                            'documentKey' => (object) ['_id' => $result->getInsertedId()]
                        ]);
        $this->assertEquals($changeStreamResult->current(), $expectedResult);

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStreamResult->getCursorId()]]);
        $operation->execute($this->getPrimaryServer());

        $result = $this->collection->insertOne(['x' => 3]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
        $expectedResult = (object) ([
                            '_id' => $changeStreamResult->current()->_id,
                            'operationType' => 'insert',
                            'fullDocument' => (object) ['_id' => $result->getInsertedId(), 'x' => 3],
                            'ns' => (object) ['db' => 'phplib_test', 'coll' => 'ChangeStreamFunctionalTest.e68b9f01'],
                            'documentKey' => (object) ['_id' => $result->getInsertedId()]
                        ]);
        $this->assertEquals($changeStreamResult->current(), $expectedResult);
   }

    public function testNoChangeAfterResumeBeforeInsert()
    {
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $result = $this->collection->insertOne(['x' => 1]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult = $this->collection->watch();
        $changeStreamResult->rewind();
        $this->assertNull($changeStreamResult->current());

        $result = $this->collection->insertOne(['x' => 2]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
        $expectedResult = (object) ([
                            '_id' => $changeStreamResult->current()->_id,
                            'operationType' => 'insert',
                            'fullDocument' => (object) ['_id' => $result->getInsertedId(), 'x' => 2],
                            'ns' => (object) ['db' => 'phplib_test', 'coll' => 'ChangeStreamFunctionalTest.4a554985'],
                            'documentKey' => (object) ['_id' => $result->getInsertedId()]
                        ]);
        $this->assertEquals($changeStreamResult->current(), $expectedResult);

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStreamResult->getCursorId()]]);
        $operation->execute($this->getPrimaryServer());

        $changeStreamResult->next();
        $this->assertNull($changeStreamResult->current());

        $result = $this->collection->insertOne(['x' => 3]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
        $expectedResult = (object) ([
                            '_id' => $changeStreamResult->current()->_id,
                            'operationType' => 'insert',
                            'fullDocument' => (object) ['_id' => $result->getInsertedId(), 'x' => 3],
                            'ns' => (object) ['db' => 'phplib_test', 'coll' => 'ChangeStreamFunctionalTest.4a554985'],
                            'documentKey' => (object) ['_id' => $result->getInsertedId()]
                        ]);
        $this->assertEquals($changeStreamResult->current(), $expectedResult);
    }

    public function testResumeAfterKillThenNoOperations()
    {
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $changeStreamResult = $this->collection->watch();

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStreamResult->getCursorId()]]);
        $operation->execute($this->getPrimaryServer());

        $changeStreamResult->next();
        $this->assertNull($changeStreamResult->current());
    }

    public function testResumeAfterKillThenOperation()
    {
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $changeStreamResult = $this->collection->watch();

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStreamResult->getCursorId()]]);
        $operation->execute($this->getPrimaryServer());

        $result = $this->collection->insertOne(['x' => 3]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
        $this->assertNull($changeStreamResult->current());
    }

    public function testKey()
    {
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $changeStreamResult = $this->collection->watch();

        $this->assertNull($changeStreamResult->key());

        $result = $this->collection->insertOne(['x' => 1]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
        $this->assertSame(1, $changeStreamResult->key());

        $changeStreamResult->next();
        $this->assertNull($changeStreamResult->key());
        $changeStreamResult->next();
        $this->assertNull($changeStreamResult->key());

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStreamResult->getCursorId()]]);
        $operation->execute($this->getPrimaryServer());

        $changeStreamResult->next();
        $this->assertNull($changeStreamResult->key());

        $result = $this->collection->insertOne(['x' => 2]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
        $this->assertSame(2, $changeStreamResult->key());
    }

    public function testNonEmptyPipeline()
    {
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $pipeline = [['$project' => ['foo' => [0]]]];
        $changeStreamResult = $this->collection->watch($pipeline, []);

        $result = $this->collection->insertOne(['x' => 1]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
        $expectedResult = (object) ([
                            '_id' => $changeStreamResult->current()->_id,
                            'foo' => [0]
                        ]);
        $this->assertEquals($changeStreamResult->current(), $expectedResult);
    }

    public function testCursorWithEmptyBatchNotClosed()
    {
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $changeStreamResult = $this->collection->watch();
        $this->assertNotNull($changeStreamResult);
    }

    /**
     * @expectedException MongoDB\Exception\ResumeTokenException
     */
    public function testFailureAfterResumeTokenRemoved()
    {
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $pipeline =  [['$project' => ['_id' => 0 ]]];
        $changeStreamResult = $this->collection->watch($pipeline, []);

        $result = $this->collection->insertOne(['x' => 1]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
    }

    public function testConnectionException()
    {
        $client = new Client($this->getUri(), ['socketTimeoutMS' => 1005], []);
        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());

        $changeStreamResult = $collection->watch();
        $changeStreamResult->next();

        $result = $collection->insertOne(['x' => 1]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
        $expectedResult = (object) ([
                            '_id' => $changeStreamResult->current()->_id,
                            'operationType' => 'insert',
                            'fullDocument' => (object) ['_id' => $result->getInsertedId(), 'x' => 1],
                            'ns' => (object) ['db' => 'phplib_test', 'coll' => 'ChangeStreamFunctionalTest.226d95f1'],
                            'documentKey' => (object) ['_id' => $result->getInsertedId()]
                        ]);
        $this->assertEquals($changeStreamResult->current(), $expectedResult);
    }

    public function testMaxAwaitTimeMS()
    {
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());
        /* On average, an acknowledged write takes about 20 ms to appear in a
         * change stream on the server so we'll use a higher maxAwaitTimeMS to
         * ensure we see the write. */
        $maxAwaitTimeMS = 100;
        $changeStreamResult = $this->collection->watch([], ['maxAwaitTimeMS' => $maxAwaitTimeMS]);

        /* The initial change stream is empty so we should expect a delay when
         * we call rewind, since it issues a getMore. Expect to wait at least
         * maxAwaitTimeMS, since no new documents should be inserted to wake up
         * the server's query thread. Also ensure we don't wait too long (server
         * default is one second). */
        $startTime = microtime(true);
        $changeStreamResult->rewind();
        $duration = microtime(true) - $startTime;
        $this->assertGreaterThanOrEqual($maxAwaitTimeMS * 0.001, $duration);
        $this->assertLessThan(0.5, $duration);

        $this->assertFalse($changeStreamResult->valid());

        /* Advancing again on a change stream will issue a getMore, so we should
         * expect a delay again. */
        $startTime = microtime(true);
        $changeStreamResult->next();
        $duration = microtime(true) - $startTime;
        $this->assertGreaterThanOrEqual($maxAwaitTimeMS * 0.001, $duration);
        $this->assertLessThan(0.5, $duration);

        $this->assertFalse($changeStreamResult->valid());

        /* After inserting a document, the change stream will not issue a
         * getMore so we should not expect a delay. */
        $result = $this->collection->insertOne(['_id' => 1]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $startTime = microtime(true);
        $changeStreamResult->next();
        $duration = microtime(true) - $startTime;
        $this->assertLessThan($maxAwaitTimeMS * 0.001, $duration);
        $this->assertTrue($changeStreamResult->valid());
    }
}
