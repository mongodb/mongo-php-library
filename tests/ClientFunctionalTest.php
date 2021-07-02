<?php

namespace MongoDB\Tests;

use MongoDB\Client;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Session;
use MongoDB\Model\DatabaseInfo;
use MongoDB\Model\DatabaseInfoIterator;

use function call_user_func;
use function is_callable;
use function sprintf;
use function version_compare;

/**
 * Functional tests for the Client class.
 */
class ClientFunctionalTest extends FunctionalTestCase
{
    /** @var Client */
    private $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createTestClient();
        $this->client->dropDatabase($this->getDatabaseName());
    }

    public function testGetManager(): void
    {
        $this->assertInstanceOf(Manager::class, $this->client->getManager());
    }

    public function testDropDatabase(): void
    {
        $bulkWrite = new BulkWrite();
        $bulkWrite->insert(['x' => 1]);

        $writeResult = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $commandResult = $this->client->dropDatabase($this->getDatabaseName());
        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionCount($this->getNamespace(), 0);
    }

    public function testListDatabases(): void
    {
        $bulkWrite = new BulkWrite();
        $bulkWrite->insert(['x' => 1]);

        $writeResult = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $databases = $this->client->listDatabases();

        $this->assertInstanceOf(DatabaseInfoIterator::class, $databases);

        foreach ($databases as $database) {
            $this->assertInstanceOf(DatabaseInfo::class, $database);
        }

        $this->assertDatabaseExists($this->getDatabaseName(), function (DatabaseInfo $info): void {
            $this->assertFalse($info->isEmpty());
            $this->assertGreaterThan(0, $info->getSizeOnDisk());
        });
    }

    public function testListDatabaseNames(): void
    {
        $bulkWrite = new BulkWrite();
        $bulkWrite->insert(['x' => 1]);

        $writeResult = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        foreach ($this->client->listDatabaseNames() as $database) {
            $this->assertIsString($database);
        }

        $this->assertContains($this->getDatabaseName(), $this->client->listDatabaseNames(), sprintf('Database %s does not exist on the server', $this->getDatabaseName()));
    }

    /**
     * Asserts that a database with the given name exists on the server.
     *
     * An optional $callback may be provided, which should take a DatabaseInfo
     * argument as its first and only parameter. If a DatabaseInfo matching
     * the given name is found, it will be passed to the callback, which may
     * perform additional assertions.
     *
     * @param string   $databaseName
     * @param callable $callback
     */
    private function assertDatabaseExists(string $databaseName, ?callable $callback = null): void
    {
        if ($callback !== null && ! is_callable($callback)) {
            throw new InvalidArgumentException('$callback is not a callable');
        }

        $databases = $this->client->listDatabases();

        $foundDatabase = null;

        foreach ($databases as $database) {
            if ($database->getName() === $databaseName) {
                $foundDatabase = $database;
                break;
            }
        }

        $this->assertNotNull($foundDatabase, sprintf('Database %s does not exist on the server', $databaseName));

        if ($callback !== null) {
            call_user_func($callback, $foundDatabase);
        }
    }

    public function testStartSession(): void
    {
        if (version_compare($this->getFeatureCompatibilityVersion(), '3.6', '<')) {
            $this->markTestSkipped('startSession() is only supported on FCV 3.6 or higher');
        }

        $this->assertInstanceOf(Session::class, $this->client->startSession());
    }
}
