<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\DropDatabase;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListCollections;

class ListCollectionsFunctionalTest extends FunctionalTestCase
{
    public function testListCollectionsForNewlyCreatedDatabase()
    {
        $server = $this->getPrimaryServer();

        $operation = new DropDatabase($this->getDatabaseName());
        $operation->execute($server);

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new ListCollections($this->getDatabaseName(), ['filter' => ['name' => $this->getCollectionName()]]);
        $collections = $operation->execute($server);

        $this->assertInstanceOf('MongoDB\Model\CollectionInfoIterator', $collections);

        $this->assertCount(1, $collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf('MongoDB\Model\CollectionInfo', $collection);
            $this->assertEquals($this->getCollectionName(), $collection->getName());
        }
    }

    public function testListCollectionsForNonexistentDatabase()
    {
        $server = $this->getPrimaryServer();

        $operation = new DropDatabase($this->getDatabaseName());
        $operation->execute($server);

        $operation = new ListCollections($this->getDatabaseName());
        $collections = $operation->execute($server);

        $this->assertCount(0, $collections);
    }
}
