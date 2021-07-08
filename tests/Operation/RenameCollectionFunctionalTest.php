<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\Exception\CommandException;
use MongoDB\Operation\DropCollection;
use MongoDB\Operation\FindOne;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListCollections;
use MongoDB\Operation\RenameCollection;
use MongoDB\Tests\CommandObserver;

use function call_user_func;
use function is_callable;
use function sprintf;
use function version_compare;

class RenameCollectionFunctionalTest extends FunctionalTestCase
{
    /** @var string */
    private $renamedCollection;

    /** @var string */
    private $renamedNamespace;

    public function setUp(): void
    {
        parent::setUp();

        $this->renamedCollection = $this->getCollectionName() . '.renamed';
        $this->renamedNamespace = $this->getNamespace() . '.renamed';
        $operation = new DropCollection($this->getDatabaseName(), $this->renamedCollection);
        $operation->execute($this->getPrimaryServer());
    }

    public function testDefaultWriteConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $server = $this->getPrimaryServer();

                $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
                $writeResult = $insertOne->execute($server);
                $this->assertEquals(1, $writeResult->getInsertedCount());

                $operation = new RenameCollection(
                    $this->getNamespace(),
                    $this->renamedNamespace,
                    ['writeConcern' => $this->createDefaultWriteConcern()]
                );

                $operation->execute($server);
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
            }
        );
    }

    public function testRenameExistingCollection(): void
    {
        $server = $this->getPrimaryServer();

        $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['_id' => 1, 'x' => 'foo']);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new RenameCollection($this->getNamespace(), $this->renamedNamespace);
        $commandResult = $operation->execute($server);

        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionDoesNotExist($this->getCollectionName());
        $this->assertCollectionExists($this->renamedCollection);

        $operation = new FindOne($this->getDatabaseName(), $this->renamedCollection, []);
        $cursor = $operation->execute($server);
        $this->assertSameDocument(['_id' => 1, 'x' => 'foo'], $cursor);
    }

    /**
     * @depends testRenameExistingCollection
     */
    public function testRenameNonexistentCollection(): void
    {
        $this->assertCollectionDoesNotExist($this->getNamespace());

        $this->expectException(CommandException::class);
        $operation = new RenameCollection($this->getNamespace(), $this->renamedNamespace);
        $commandResult = $operation->execute($this->getPrimaryServer());

        /* Avoid inspecting the result document as mongos returns {ok:1.0},
         * which is inconsistent from the expected mongod response of {ok:0}. */
        $this->assertIsObject($commandResult);
    }

    public function testSessionOption(): void
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver())->observe(
            function (): void {
                $server = $this->getPrimaryServer();

                $insertOne = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1]);
                $writeResult = $insertOne->execute($server);
                $this->assertEquals(1, $writeResult->getInsertedCount());

                $operation = new RenameCollection(
                    $this->getNamespace(),
                    $this->renamedNamespace,
                    ['session' => $this->createSession()]
                );

                $operation->execute($server);
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }

    public function tearDown(): void
    {
        if ($this->hasFailed()) {
            return;
        }

        $operation = new DropCollection($this->getDatabaseName(), $this->renamedCollection);
        $operation->execute($this->getPrimaryServer());

        parent::tearDown();
    }

    /**
     * Asserts that a collection with the given name does not exist on the
     * server.
     *
     * @param string $collectionName
     */
    private function assertCollectionDoesNotExist(string $collectionName): void
    {
        $operation = new ListCollections($this->getDatabaseName());
        $collections = $operation->execute($this->getPrimaryServer());

        $foundCollection = null;

        foreach ($collections as $collection) {
            if ($collection->getName() === $collectionName) {
                $foundCollection = $collection;
                break;
            }
        }

        $this->assertNull($foundCollection, sprintf('Collection %s exists', $collectionName));
    }

    /**
     * Asserts that a collection with the given name exists on the server.
     *
     * An optional $callback may be provided, which should take a CollectionInfo
     * argument as its first and only parameter. If a CollectionInfo matching
     * the given name is found, it will be passed to the callback, which may
     * perform additional assertions.
     *
     * @param callable $callback
     */
    private function assertCollectionExists($collectionName, ?callable $callback = null): void
    {
        if ($callback !== null && ! is_callable($callback)) {
            throw new InvalidArgumentException('$callback is not a callable');
        }

        $operation = new ListCollections($this->getDatabaseName());
        $collections = $operation->execute($this->getPrimaryServer());

        $foundCollection = null;

        foreach ($collections as $collection) {
            if ($collection->getName() === $collectionName) {
                $foundCollection = $collection;
                break;
            }
        }

        $this->assertNotNull($foundCollection, sprintf('Found %s collection in the database', $collectionName));

        if ($callback !== null) {
            call_user_func($callback, $foundCollection);
        }
    }
}
