<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\Server;
use MongoDB\Operation\DropDatabase;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListDatabases;
use MongoDB\Tests\CommandObserver;
use stdClass;

class DropDatabaseFunctionalTest extends FunctionalTestCase
{
    public function testDefaultWriteConcernIsOmitted()
    {
        (new CommandObserver)->observe(
            function() {
                $operation = new DropDatabase(
                    $this->getDatabaseName(),
                    ['writeConcern' => $this->createDefaultWriteConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(stdClass $command) {
                $this->assertObjectNotHasAttribute('writeConcern', $command);
            }
        );
    }

    public function testDropExistingDatabase()
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
    public function testDropNonexistentDatabase()
    {
        $server = $this->getPrimaryServer();

        $operation = new DropDatabase($this->getDatabaseName());
        $operation->execute($server);

        $this->assertDatabaseDoesNotExist($server, $this->getDatabaseName());

        $operation = new DropDatabase($this->getDatabaseName());
        $operation->execute($server);
    }

    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver)->observe(
            function() {
                $operation = new DropDatabase(
                    $this->getDatabaseName(),
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(stdClass $command) {
                $this->assertObjectHasAttribute('lsid', $command);
            }
        );
    }

    /**
     * Asserts that a database with the given name does not exist on the server.
     *
     * @param Server $server
     * @param string $databaseName
     */
    private function assertDatabaseDoesNotExist(Server $server, $databaseName)
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
