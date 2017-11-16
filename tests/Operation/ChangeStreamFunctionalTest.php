<?php

namespace MongoDB\Tests\Operation;

use MongoDB\ChangeStream;
use MongoDB\ChangeStreamIterator;
use MongoDB\Collection;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\ReadPreference;
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
        if (version_compare($this->getFeatureCompatibilityVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('$changeStream is only supported on FCV 3.6 or higher');
        }
   }

    public function testChangeStreamResume()
    {
        $operation = new DatabaseCommand("admin", ["setFeatureCompatibilityVersion" => "3.6"]);
        $operation->execute($this->getPrimaryServer());

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

    public function testChangeStreamAfterResumeBeforeInsert()
    {
        $operation = new DatabaseCommand("admin", ["setFeatureCompatibilityVersion" => "3.6"]);
        $operation->execute($this->getPrimaryServer());

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
}
