<?php

namespace MongoDB\Tests\Operation;

use InvalidArgumentException;
use MongoDB\Model\IndexInfo;
use MongoDB\Operation\CreateIndexes;
use MongoDB\Operation\DropIndexes;
use MongoDB\Operation\ListIndexes;
use MongoDB\Tests\CommandObserver;
use function call_user_func;
use function is_callable;
use function sprintf;
use function version_compare;

class DropIndexesFunctionalTest extends FunctionalTestCase
{
    public function testDefaultWriteConcernIsOmitted()
    {
        $operation = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [['key' => ['x' => 1]]]);
        $operation->execute($this->getPrimaryServer());

        (new CommandObserver())->observe(
            function () {
                $operation = new DropIndexes(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    'x_1',
                    ['writeConcern' => $this->createDefaultWriteConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
            }
        );
    }

    public function testDropOneIndexByName()
    {
        $indexes = [['key' => ['x' => 1]]];

        $operation = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), $indexes);
        $createdIndexNames = $operation->execute($this->getPrimaryServer());

        $this->assertSame('x_1', $createdIndexNames[0]);
        $this->assertIndexExists('x_1');

        $operation = new DropIndexes($this->getDatabaseName(), $this->getCollectionName(), 'x_1');
        $this->assertCommandSucceeded($operation->execute($this->getPrimaryServer()));

        $operation = new ListIndexes($this->getDatabaseName(), $this->getCollectionName());
        $indexes = $operation->execute($this->getPrimaryServer());

        foreach ($indexes as $index) {
            if ($index->getName() === 'x_1') {
                $this->fail('The "x_1" index should have been deleted');
            }
        }
    }

    public function testDropAllIndexesByWildcard()
    {
        $indexes = [
            ['key' => ['x' => 1]],
            ['key' => ['y' => 1]],
        ];

        $operation = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), $indexes);
        $createdIndexNames = $operation->execute($this->getPrimaryServer());

        $this->assertSame('x_1', $createdIndexNames[0]);
        $this->assertSame('y_1', $createdIndexNames[1]);
        $this->assertIndexExists('x_1');
        $this->assertIndexExists('y_1');

        $operation = new DropIndexes($this->getDatabaseName(), $this->getCollectionName(), '*');
        $this->assertCommandSucceeded($operation->execute($this->getPrimaryServer()));

        $operation = new ListIndexes($this->getDatabaseName(), $this->getCollectionName());
        $indexes = $operation->execute($this->getPrimaryServer());

        foreach ($indexes as $index) {
            if ($index->getName() === 'x_1') {
                $this->fail('The "x_1" index should have been deleted');
            }

            if ($index->getName() === 'y_1') {
                $this->fail('The "y_1" index should have been deleted');
            }
        }
    }

    public function testDropByIndexInfo()
    {
        $info = new IndexInfo([
            'v' => 1,
            'key' => ['x' => 1],
            'name' => 'x_1',
            'ns' => 'foo.bar',
        ]);

        $operation = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [['key' => ['x' => 1]]]);
        $createdIndexNames = $operation->execute($this->getPrimaryServer());

        $this->assertSame('x_1', $createdIndexNames[0]);
        $this->assertIndexExists('x_1');

        $operation = new DropIndexes($this->getDatabaseName(), $this->getCollectionName(), $info);
        $this->assertCommandSucceeded($operation->execute($this->getPrimaryServer()));

        $operation = new ListIndexes($this->getDatabaseName(), $this->getCollectionName());
        $indexes = $operation->execute($this->getPrimaryServer());

        foreach ($indexes as $index) {
            if ($index->getName() === 'x_1') {
                $this->fail('The "x_1" index should have been deleted');
            }
        }
    }

    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        $operation = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [['key' => ['x' => 1]]]);
        $operation->execute($this->getPrimaryServer());

        (new CommandObserver())->observe(
            function () {
                $operation = new DropIndexes(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    '*',
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }

    /**
     * Asserts that an index with the given name exists for the collection.
     *
     * An optional $callback may be provided, which should take an IndexInfo
     * argument as its first and only parameter. If an IndexInfo matching the
     * given name is found, it will be passed to the callback, which may perform
     * additional assertions.
     *
     * @param callable $callback
     */
    private function assertIndexExists($indexName, $callback = null)
    {
        if ($callback !== null && ! is_callable($callback)) {
            throw new InvalidArgumentException('$callback is not a callable');
        }

        $operation = new ListIndexes($this->getDatabaseName(), $this->getCollectionName());
        $indexes = $operation->execute($this->getPrimaryServer());

        $foundIndex = null;

        foreach ($indexes as $index) {
            if ($index->getName() === $indexName) {
                $foundIndex = $index;
                break;
            }
        }

        $this->assertNotNull($foundIndex, sprintf('Found %s index for the collection', $indexName));

        if ($callback !== null) {
            call_user_func($callback, $foundIndex);
        }
    }
}
