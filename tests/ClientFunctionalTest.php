<?php

namespace MongoDB\Tests;

use MongoDB\Client;

/**
 * Functional tests for the Client class.
 */
class ClientFunctionalTest extends FunctionalTestCase
{
    public function testDropDatabase()
    {
        $writeResult = $this->manager->executeInsert($this->getNamespace(), array('x' => 1));
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $client = new Client($this->getUri());
        $commandResult = $client->dropDatabase($this->getDatabaseName());
        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionCount($this->getNamespace(), 0);
    }

    public function testListDatabases()
    {
        $writeResult = $this->manager->executeInsert($this->getNamespace(), array('x' => 1));
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $client = new Client($this->getUri());
        $databases = $client->listDatabases();

        $this->assertInstanceOf('Traversable', $databases);

        $foundDatabase = null;

        foreach ($databases as $database) {
            if ($database['name'] === $this->getDatabaseName()) {
                $foundDatabase = $database;
                break;
            }
        }

        $this->assertNotNull($foundDatabase, 'Found test database in list of databases');
        $this->assertFalse($foundDatabase['empty'], 'Test database is not empty');
        $this->assertGreaterThan(0, $foundDatabase['sizeOnDisk'], 'Test database takes up disk space');
    }
}
