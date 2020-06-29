<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListDatabaseNames;
use MongoDB\Tests\CommandObserver;
use function version_compare;

class ListDatabaseNamesFunctionalTest extends FunctionalTestCase
{
    public function testListDatabaseNames()
    {
        $server = $this->getPrimaryServer();

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $databases = null;
        (new CommandObserver())->observe(
            function () use (&$databases, $server) {
                $operation = new ListDatabaseNames();

                $databases = $operation->execute($server);
            },
            function (array $event) {
                $this->assertObjectNotHasAttribute('authorizedDatabases', $event['started']->getCommand());
                $this->assertSame(true, $event['started']->getCommand()->nameOnly);
            }
        );

        foreach ($databases as $database) {
            $this->assertIsString($database);
        }
    }

    public function testAuthorizedDatabasesOption()
    {
        (new CommandObserver())->observe(
            function () {
                $operation = new ListDatabaseNames(
                    ['authorizedDatabases' => true]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) {
                $this->assertObjectHasAttribute('authorizedDatabases', $event['started']->getCommand());
                $this->assertSame(true, $event['started']->getCommand()->nameOnly);
            }
        );
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

        $operation = new ListDatabaseNames(['filter' => ['name' => $this->getDatabaseName()]]);
        $names = $operation->execute($server);
        $this->assertCount(1, $names);

        foreach ($names as $database) {
            $this->assertSame($this->getDatabaseName(), $database);
        }
    }

    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver())->observe(
            function () {
                $operation = new ListDatabaseNames(
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }
}
