<?php

namespace MongoDB\Tests\Operation;

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
    public function testDefaultWriteConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new RenameCollection(
                    'admin',
                    $this->getCollectionName(),
                    $this->getCollectionName() . '.renamed',
                    ['writeConcern' => $this->createDefaultWriteConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
            }
        );
    }

    public function testRenameExistingCollection(): void
    {
        $that = $this;
        $renamedCollectionName = $this->getCollectionName() . '.renamed';
        $server = $this->getPrimaryServer();

        $insertOne = new InsertOne('admin', $this->getCollectionName(), ['x' => 1]);
        $writeResult = $insertOne->execute($server);
        $this->assertEquals(1, $writeResult->getInsertedCount());

        $operation = new RenameCollection('admin', $this->getCollectionName(), $renamedCollectionName);
        $commandResult = $operation->execute($server);

        $this->assertCommandSucceeded($commandResult);
        $this->assertCollectionDoesNotExist($this->getCollectionName());
        $this->assertCollectionExists($renamedCollectionName);
        // $this->assertCollectionExists($renamedCollectionName, function (CollectionInfo $info) use ($that) {
        //     $this->assertSame(1, $info->offsetGet('x'));
        // });
    }

    /**
     * @depends testRenameExistingCollection
     */
    public function testRenameNonexistentCollection(): void
    {
        $this->assertCollectionDoesNotExist($this->getCollectionName());

        $operation = new RenameCollection('admin', $this->getCollectionName(), $this->getCollectionName() . '.renamed');
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
                $operation = new RenameCollection(
                    'admin',
                    $this->getCollectionName(),
                    $this->getCollectionName() . '.renamed',
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
     * Asserts that a collection with the given name does not exist on the
     * server.
     *
     * @param string $collectionName
     */
    private function assertCollectionDoesNotExist(string $collectionName): void
    {
        $operation = new ListCollections('admin');
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
     * Asserts that a collection with the given name exists on the serv er.
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

        $operation = new ListCollections('admin');
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
