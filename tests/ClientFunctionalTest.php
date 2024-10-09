<?php

namespace MongoDB\Tests;

use Iterator;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Client;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Session;
use MongoDB\Model\DatabaseInfo;

use function call_user_func;
use function is_callable;
use function iterator_to_array;
use function sprintf;

/**
 * Functional tests for the Client class.
 */
class ClientFunctionalTest extends FunctionalTestCase
{
    private Client $client;

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

        $this->client->dropDatabase($this->getDatabaseName());
        $this->assertCollectionCount($this->getNamespace(), 0);
    }

    public function testListDatabases(): void
    {
        $bulkWrite = new BulkWrite();
        $bulkWrite->insert(['x' => 1]);

        $writeResult = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $databases = $this->client->listDatabases();

        $this->assertInstanceOf(Iterator::class, $databases);

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
        $this->assertInstanceOf(Session::class, $this->client->startSession());
    }

    public function testAddAndRemoveSubscriber(): void
    {
        $client = static::createTestClient();

        $addedSubscriber = $this->createMock(CommandSubscriber::class);
        $addedSubscriber->expects($this->once())->method('commandStarted');
        $client->addSubscriber($addedSubscriber);

        $removedSubscriber = $this->createMock(CommandSubscriber::class);
        $removedSubscriber->expects($this->never())->method('commandStarted');
        $client->addSubscriber($removedSubscriber);
        $client->removeSubscriber($removedSubscriber);

        $client->getManager()->executeCommand('admin', new Command(['ping' => 1]));
    }

    public function testWatchWithBuilderPipeline(): void
    {
        $this->skipIfChangeStreamIsNotSupported();

        if ($this->isShardedCluster()) {
            $this->markTestSkipped('Test does not apply on sharded clusters: need more than a single getMore call on the change stream.');
        }

        $pipeline = new Pipeline(
            Stage::match(operationType: Query::eq('insert')),
        );
        // Extract the list of stages for arg type restriction
        $pipeline = iterator_to_array($pipeline);

        $changeStream = $this->client->watch($pipeline);
        $this->client->selectCollection($this->getDatabaseName(), $this->getCollectionName())->insertOne(['x' => 3]);
        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertEquals('insert', $changeStream->current()->operationType);
    }
}
