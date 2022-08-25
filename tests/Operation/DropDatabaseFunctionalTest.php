<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\Server;
use MongoDB\Operation\DropDatabase;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListDatabases;
use MongoDB\Tests\CommandObserver;

use function sprintf;

class DropDatabaseFunctionalTest extends FunctionalTestCase
{
    public function testDefaultWriteConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new DropDatabase(
                    $this->getDatabaseName(),
                    ['writeConcern' => $this->createDefaultWriteConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
            }
        );
    }

    public function testDropExistingDatabase(): void
    {
        $server = $this->getPrimaryServer();

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new DropDatabase($this->getDatabaseName());
        $operation->execute($server);

        $this->assertDatabaseDoesNotExist($server, $this->getDatabaseName());
    }

    /**
     * @depends testDropExistingDatabase
     */
    public function testDropNonexistentDatabase(): void
    {
        $server = $this->getPrimaryServer();

        $operation = new DropDatabase($this->getDatabaseName());
        $operation->execute($server);

        $this->assertDatabaseDoesNotExist($server, $this->getDatabaseName());

        $operation = new DropDatabase($this->getDatabaseName());
        $operation->execute($server);
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new DropDatabase(
                    $this->getDatabaseName(),
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }

    /**
     * Asserts that a database with the given name does not exist on the server.
     */
    private function assertDatabaseDoesNotExist(Server $server, string $databaseName): void
    {
        $operation = new ListDatabases();
        $databases = $operation->execute($server);

        $foundDatabase = null;

        foreach ($databases as $database) {
            if ($database->getName() === $databaseName) {
                $foundDatabase = $database;
                break;
            }
        }

        $this->assertNull($foundDatabase, sprintf('Database %s exists on the server', $databaseName));
    }
}
