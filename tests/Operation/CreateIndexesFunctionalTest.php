<?php

namespace MongoDB\Tests\Operation;

use InvalidArgumentException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Model\IndexInfo;
use MongoDB\Operation\CreateIndexes;
use MongoDB\Operation\ListIndexes;
use MongoDB\Tests\CommandObserver;

use function call_user_func;
use function is_callable;
use function sprintf;
use function version_compare;

class CreateIndexesFunctionalTest extends FunctionalTestCase
{
    public function testCreateSparseUniqueIndex(): void
    {
        $indexes = [['key' => ['x' => 1], 'sparse' => true, 'unique' => true]];

        $operation = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), $indexes);
        $createdIndexNames = $operation->execute($this->getPrimaryServer());

        $this->assertSame('x_1', $createdIndexNames[0]);
        $this->assertIndexExists('x_1', function (IndexInfo $info): void {
            $this->assertTrue($info->isSparse());
            $this->assertTrue($info->isUnique());
            $this->assertFalse($info->isTtl());
        });
    }

    public function testCreateCompoundIndex(): void
    {
        $indexes = [['key' => ['y' => -1, 'z' => 1]]];

        $operation = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), $indexes);
        $createdIndexNames = $operation->execute($this->getPrimaryServer());

        $this->assertSame('y_-1_z_1', $createdIndexNames[0]);
        $this->assertIndexExists('y_-1_z_1', function (IndexInfo $info): void {
            $this->assertFalse($info->isSparse());
            $this->assertFalse($info->isUnique());
            $this->assertFalse($info->isTtl());
        });
    }

    public function testCreateGeospatialIndex(): void
    {
        $indexes = [['key' => ['g' => '2dsphere', 'z' => 1]]];

        $operation = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), $indexes);
        $createdIndexNames = $operation->execute($this->getPrimaryServer());

        $this->assertSame('g_2dsphere_z_1', $createdIndexNames[0]);
        $this->assertIndexExists('g_2dsphere_z_1', function (IndexInfo $info): void {
            $this->assertFalse($info->isSparse());
            $this->assertFalse($info->isUnique());
            $this->assertFalse($info->isTtl());
        });
    }

    public function testCreateTTLIndex(): void
    {
        $indexes = [['key' => ['t' => 1], 'expireAfterSeconds' => 0, 'name' => 'my_ttl']];

        $operation = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), $indexes);
        $createdIndexNames = $operation->execute($this->getPrimaryServer());

        $this->assertSame('my_ttl', $createdIndexNames[0]);
        $this->assertIndexExists('my_ttl', function (IndexInfo $info): void {
            $this->assertFalse($info->isSparse());
            $this->assertFalse($info->isUnique());
            $this->assertTrue($info->isTtl());
        });
    }

    public function testCreateIndexes(): void
    {
        $expectedNames = ['x_1', 'y_-1_z_1', 'g_2dsphere_z_1', 'my_ttl'];

        $indexes = [
            ['key' => ['x' => 1], 'sparse' => true, 'unique' => true],
            ['key' => ['y' => -1, 'z' => 1]],
            ['key' => ['g' => '2dsphere', 'z' => 1]],
            ['key' => ['t' => 1], 'expireAfterSeconds' => 0, 'name' => 'my_ttl'],
        ];

        $operation = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), $indexes);
        $createdIndexNames = $operation->execute($this->getPrimaryServer());

        $this->assertSame($expectedNames, $createdIndexNames);

        $this->assertIndexExists('x_1', function (IndexInfo $info): void {
            $this->assertTrue($info->isSparse());
            $this->assertTrue($info->isUnique());
            $this->assertFalse($info->isTtl());
        });

        $this->assertIndexExists('y_-1_z_1', function (IndexInfo $info): void {
            $this->assertFalse($info->isSparse());
            $this->assertFalse($info->isUnique());
            $this->assertFalse($info->isTtl());
        });

        $this->assertIndexExists('g_2dsphere_z_1', function (IndexInfo $info): void {
            $this->assertFalse($info->isSparse());
            $this->assertFalse($info->isUnique());
            $this->assertFalse($info->isTtl());
        });

        $this->assertIndexExists('my_ttl', function (IndexInfo $info): void {
            $this->assertFalse($info->isSparse());
            $this->assertFalse($info->isUnique());
            $this->assertTrue($info->isTtl());
        });
    }

    public function testCreateConflictingIndexesWithCommand(): void
    {
        $indexes = [
            ['key' => ['x' => 1], 'sparse' => true, 'unique' => false],
            ['key' => ['x' => 1], 'sparse' => false, 'unique' => true],
        ];

        $operation = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), $indexes);

        $this->expectException(RuntimeException::class);
        $operation->execute($this->getPrimaryServer());
    }

    public function testDefaultWriteConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new CreateIndexes(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['key' => ['x' => 1]]],
                    ['writeConcern' => $this->createDefaultWriteConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
            }
        );
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new CreateIndexes(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['key' => ['x' => 1]]],
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }

    public function testCommitQuorumOption(): void
    {
        if (version_compare($this->getServerVersion(), '4.3.4', '<')) {
            $this->markTestSkipped('commitQuorum is not supported');
        }

        if ($this->getPrimaryServer()->getType() !== Server::TYPE_RS_PRIMARY) {
            $this->markTestSkipped('commitQuorum is only supported on replica sets');
        }

        (new CommandObserver())->observe(
            function (): void {
                $operation = new CreateIndexes(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['key' => ['x' => 1]]],
                    ['commitQuorum' => 'majority']
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('commitQuorum', $event['started']->getCommand());
            }
        );
    }

    public function testCommitQuorumUnsupported(): void
    {
        if (version_compare($this->getServerVersion(), '4.3.4', '>=')) {
            $this->markTestSkipped('commitQuorum is supported');
        }

        $operation = new CreateIndexes(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            [['key' => ['x' => 1]]],
            ['commitQuorum' => 'majority']
        );

        $this->expectException(UnsupportedException::class);
        $this->expectExceptionMessage('The "commitQuorum" option is not supported by the server executing this operation');

        $operation->execute($this->getPrimaryServer());
    }

    /**
     * Asserts that an index with the given name exists for the collection.
     *
     * An optional $callback may be provided, which should take an IndexInfo
     * argument as its first and only parameter. If an IndexInfo matching the
     * given name is found, it will be passed to the callback, which may perform
     * additional assertions.
     *
     * @param string   $indexName
     * @param callable $callback
     */
    private function assertIndexExists(string $indexName, ?callable $callback = null): void
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

        $this->assertNotNull($foundIndex, sprintf('Index %s does not exist', $indexName));

        if ($callback !== null) {
            call_user_func($callback, $foundIndex);
        }
    }
}
