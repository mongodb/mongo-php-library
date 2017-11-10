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
    public function testChangeStream()
    {
        $op = new DatabaseCommand("admin", ["setFeatureCompatibilityVersion" => "3.6"]);
        $op->execute($this->getPrimaryServer());

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());

        $this->collection->insertOne(['x' => 1]);

        $changeStreamResult = $this->collection->watch();

        $changeStreamResult->rewind();

        print("\n---SHOULD BE NULL---\n\n");
        var_dump($changeStreamResult->current());

        $this->collection->insertOne(['x' => 2]);

        $changeStreamResult->next();

        print("\n---SHOULD NOT BE NULL---\n");
        var_dump($changeStreamResult->current());

        $op1 = new DatabaseCommand($this->getDatabaseName(), ["killCursors" => $this->getCollectionName(), "cursors" => [$changeStreamResult->getId()]]);
        $op1->execute($this->getPrimaryServer());

        print("\n---SHOULD RESUME---\n");

        $changeStreamResult->next();

        $this->collection->insertOne(['x' => 3]);

        $changeStreamResult->next();

        var_dump($changeStreamResult->current());
   }
}
