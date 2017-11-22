<?php

namespace MongoDB\Tests\Operation;

use MongoDB\ChangeStream;
use MongoDB\ChangeStreamIterator;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\ReadPreference;
use MongoDB\Exception\ResumeTokenException;
use MongoDB\Operation\Aggregate;
use MongoDB\Operation\ChangeStreamCommand;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Operation\InsertOne;
use MongoDB\Tests\CommandObserver;
use IteratorIterator;
use stdClass;

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
        $this->assertNotNull($changeStreamResult->current());

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStreamResult->getId()]]);
        $operation->execute($this->getPrimaryServer());

        $result = $this->collection->insertOne(['x' => 3]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
        $this->assertNotNull($changeStreamResult->current());
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
        $this->assertNotNull($changeStreamResult->current());

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStreamResult->getId()]]);
        $operation->execute($this->getPrimaryServer());

        $changeStreamResult->next();
        $this->assertNull($changeStreamResult->current());

        $result = $this->collection->insertOne(['x' => 3]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
        $this->assertNotNull($changeStreamResult->current());
    }

    public function testResumeAfterKillThenNoOperations()
    {
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $changeStreamResult = $this->collection->watch();

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStreamResult->getId()]]);
        $operation->execute($this->getPrimaryServer());

        $changeStreamResult->next();
        $this->assertNull($changeStreamResult->current());
    }

    public function testResumeAfterKillThenOperation()
    {
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $changeStreamResult = $this->collection->watch();

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStreamResult->getId()]]);
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

        $this->assertSame(null, $changeStreamResult->key());

        $result = $this->collection->insertOne(['x' => 1]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
        $this->assertSame(1, $changeStreamResult->key());

        $changeStreamResult->next();
        $this->assertSame(null, $changeStreamResult->key());
        $changeStreamResult->next();
        $this->assertSame(null, $changeStreamResult->key());

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStreamResult->getId()]]);
        $operation->execute($this->getPrimaryServer());

        $changeStreamResult->next();
        $this->assertSame(null, $changeStreamResult->key());

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
        $this->assertNotNull($changeStreamResult->current());
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
        $this->assertNull($changeStreamResult->current());
    }
}
