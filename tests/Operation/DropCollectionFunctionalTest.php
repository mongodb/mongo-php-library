<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\Server;
use MongoDB\Operation\DropCollection;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListCollections;

class DropCollectionFunctionalTest extends FunctionalTestCase
{
    public function testDropExistingCollection()
    {
        $server = $this->getPrimaryServer();

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($server);

        $this->assertCollectionDoesNotExist($server, $this->getDatabaseName(), $this->getCollectionName());
    }

    /**
     * @depends testDropExistingCollection
     */
    public function testDropNonexistentCollection()
    {
        $server = $this->getPrimaryServer();

        $this->assertCollectionDoesNotExist($server, $this->getDatabaseName(), $this->getCollectionName());

        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($server);
    }

    /**
     * Asserts that a collection with the given name does not exist on the
     * server.
     *
     * @param Server $server
     * @param string $databaseName
     * @param string $collectionName
     */
    private function assertCollectionDoesNotExist(Server $server, $databaseName, $collectionName)
    {
        $operation = new ListCollections($databaseName);
        $collections = $operation->execute($server);

        $foundCollection = null;

        foreach ($collections as $collection) {
            if ($collection->getName() === $collectionName) {
                $foundCollection = $collection;
                break;
            }
        }

        $this->assertNull($foundCollection, sprintf('Collection %s exists on the server', $collectionName));
    }
}
