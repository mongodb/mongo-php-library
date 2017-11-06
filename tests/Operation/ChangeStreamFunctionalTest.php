<?php

namespace MongoDB\Tests\Operation;

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

        $op1 = $this->collection->insertOne(['x' => 1]);

        $changeStreamResult = $this->collection->watch();

        $changeStreamResult->rewind();
        var_dump($changeStreamResult->current());
        print("\n\n");
        $changeStreamResult->next();

        $this->collection->insertOne(['x' => 2]);

        $changeStreamResult->next();
        var_dump($changeStreamResult->current());
    }
}
