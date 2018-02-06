<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListDatabases;
use MongoDB\Tests\CommandObserver;
use stdClass;

class ListDatabasesFunctionalTest extends FunctionalTestCase
{
    public function testListDatabases()
    {
        $server = $this->getPrimaryServer();

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new ListDatabases();
        $databases = $operation->execute($server);

        $this->assertInstanceOf('MongoDB\Model\DatabaseInfoIterator', $databases);

        foreach ($databases as $database) {
            $this->assertInstanceOf('MongoDB\Model\DatabaseInfo', $database);
        }
    }

    public function testFilterOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('listDatabase command "filter" option is not supported');
        }

        $server = $this->getPrimaryServer();

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new ListDatabases(['filter' => ['name' => $this->getDatabaseName()]]);
        $databases = $operation->execute($server);

        $this->assertInstanceOf('MongoDB\Model\DatabaseInfoIterator', $databases);

        $this->assertCount(1, $databases);

        foreach ($databases as $database) {
            $this->assertInstanceOf('MongoDB\Model\DatabaseInfo', $database);
            $this->assertEquals($this->getDatabaseName(), $database->getName());
        }
    }

    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver)->observe(
            function() {
                $operation = new ListDatabases(
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(stdClass $command) {
                $this->assertObjectHasAttribute('lsid', $command);
            }
        );
    }
}
