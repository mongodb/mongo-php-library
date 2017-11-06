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
//$cSR should be empty right now
        $changeStreamResult->rewind();
        var_dump($changeStreamResult->current());
        $changeStreamResult->next();

        $op2 = $this->collection->deleteOne(['x' => 1]);
// now $cSR should show deletion
        $changeStreamResult->next();
        var_dump($changeStreamResult->toArray());
    }
}
