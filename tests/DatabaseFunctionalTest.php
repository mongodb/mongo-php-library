<?php

namespace MongoDB\Tests;

use MongoDB\Client;
use MongoDB\Database;

/**
 * Functional tests for the Database class.
 */
class DatabaseFunctionalTest extends FunctionalTestCase
{
    private $database;

    public function setUp()
    {
        parent::setUp();

        $this->database = new Database($this->manager, $this->getDatabaseName());
        $this->database->drop();
    }

    public function testDrop()
    {
        $writeResult = $this->manager->executeInsert($this->getNamespace(), array('x' => 1));
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $commandResult = $this->database->drop();
        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionCount($this->getNamespace(), 0);
    }

    public function testDropCollection()
    {
        $writeResult = $this->manager->executeInsert($this->getNamespace(), array('x' => 1));
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $commandResult = $this->database->dropCollection($this->getCollectionName());
        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionCount($this->getNamespace(), 0);
    }

    public function testListCollections()
    {
        $writeResult = $this->manager->executeInsert($this->getNamespace(), array('x' => 1));
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $collections = $this->database->listCollections();
        $this->assertInstanceOf('MongoDB\Model\CollectionInfoIterator', $collections);

        $foundCollection = null;

        foreach ($collections as $collection) {
            if ($collection->getName() === $this->getCollectionName()) {
                $foundCollection = $collection;
                break;
            }
        }

        $this->assertNotNull($foundCollection, 'Found test collection in list of collection');
    }
}
