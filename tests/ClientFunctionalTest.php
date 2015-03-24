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
}
