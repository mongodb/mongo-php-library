<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Model\DatabaseInfo;
use MongoDB\Model\DatabaseInfoIterator;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListDatabases;
use MongoDB\Tests\CommandObserver;

use function version_compare;

class ListDatabasesFunctionalTest extends FunctionalTestCase
{
    public function testListDatabases(): void
    {
        $server = $this->getPrimaryServer();

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $databases = null;
        (new CommandObserver())->observe(
            function () use (&$databases, $server): void {
                $operation = new ListDatabases();

                $databases = $operation->execute($server);
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('authorizedDatabases', $event['started']->getCommand());
            }
        );

        $this->assertInstanceOf(DatabaseInfoIterator::class, $databases);

        foreach ($databases as $database) {
            $this->assertInstanceOf(DatabaseInfo::class, $database);
        }
    }

    public function testAuthorizedDatabasesOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new ListDatabases(
                    ['authorizedDatabases' => true]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('authorizedDatabases', $event['started']->getCommand());
            }
        );
    }

    public function testFilterOption(): void
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

        $this->assertInstanceOf(DatabaseInfoIterator::class, $databases);

        $this->assertCount(1, $databases);

        foreach ($databases as $database) {
            $this->assertInstanceOf(DatabaseInfo::class, $database);
            $this->assertEquals($this->getDatabaseName(), $database->getName());
        }
    }

    public function testSessionOption(): void
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver())->observe(
            function (): void {
                $operation = new ListDatabases(
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }
}
