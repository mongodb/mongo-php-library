<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\ReadPreference;
use MongoDB\Operation\ChangeStream;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Tests\CommandObserver;
use stdClass;

class ChangeStreamFunctionalTest extends FunctionalTestCase
{
    public function testDefaultReadConcernIsOmitted()
    {
        $op = new DatabaseCommand("admin", ["setFeatureCompatibilityVersion" => "3.6"]);
        $op->execute($this->getPrimaryServer());

        $bulkWrite = new BulkWrite;
        $bulkWrite->insert(['_id' => 1, 'x' => 1]);
        $bulkWrite->insert(['_id' => 2, 'x' => 2]);
        $bulkWrite->insert(['_id' => 3, 'y' => 3]);
        $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);
        $operation = new ChangeStream($this->getDatabaseName(), $this->getCollectionName(), [['$match' => ['x' => 1]]], ['fullDocument' => 'default']);
        $changeStreamResult = $operation->execute($this->getPrimaryServer());
    }

    public function testEmptyPipeline()
    {
        $bulkWrite = new BulkWrite;
        $bulkWrite->insert(['_id' => 1, 'x' => 1]);
        $bulkWrite->insert(['_id' => 2, 'x' => 2]);
        $bulkWrite->insert(['_id' => 3, 'y' => 3]);
        $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);
        $operation = new ChangeStream($this->getDatabaseName(), $this->getCollectionName(), []);
        $changeStreamResult = $operation->execute($this->getPrimaryServer());
    }
}
