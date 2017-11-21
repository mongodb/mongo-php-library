<?php

namespace MongoDB\Tests\Operation;

use MongoDB\ChangeStream;
use MongoDB\ChangeStreamIterator;
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

    public function testChangeStreamResume()
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

    public function testChangeStreamAfterResumeBeforeInsert()
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

    public function test_resume_after_kill_then_no_operations()
    {
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $changeStreamResult = $this->collection->watch();

        $operation = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStreamResult->getId()]]);
        $operation->execute($this->getPrimaryServer());

        $changeStreamResult->next();
        $this->assertNull($changeStreamResult->current());
    }

    public function test_resume_after_kill_then_insert()
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

    public function test_key()
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

    public function test_pipeline()
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

    public function cursor_with_empty_batch_not_closed()
    {
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $changeStreamResult = $this->collection->watch();
        $this->assertNotNull($changeStreamResult);
    }

    /**
     * @expectedException MongoDB\Exception\ResumeTokenException
     */
    public function test_resume_token_removed()
    {
        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $pipeline =  [['$project' => ['_id' => 0 ]]];
        $changeStreamResult = $this->collection->watch($pipeline, []);

        $result = $this->collection->insertOne(['x' => 1]);
        $this->assertInstanceOf('MongoDB\InsertOneResult', $result);
        $this->assertSame(1, $result->getInsertedCount());

        $changeStreamResult->next();
        // expect error after call to next because resumeToken should be missing
        // (due to the pipeline)
    }
}
