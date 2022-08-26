<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Collection;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Operation\Aggregate;
use MongoDB\Tests\CommandObserver;
use stdClass;

use function current;
use function iterator_to_array;

class AggregateFunctionalTest extends FunctionalTestCase
{
    public function testAllowDiskUseIsOmittedByDefault(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Aggregate(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['$match' => ['x' => 1]]]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('allowDiskUse', $event['started']->getCommand());
            }
        );
    }

    public function testBatchSizeIsIgnoredIfPipelineIncludesOutStage(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Aggregate(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['$out' => $this->getCollectionName() . '.output']],
                    ['batchSize' => 0]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertEquals(new stdClass(), $event['started']->getCommand()->cursor);
            }
        );

        $outCollection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName() . '.output');
        $outCollection->drop();
    }

    public function testCurrentOpCommand(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Aggregate(
                    'admin',
                    null,
                    [['$currentOp' => (object) []]]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertSame(1, $event['started']->getCommand()->aggregate);
            }
        );
    }

    public function testDefaultReadConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Aggregate(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['$match' => ['x' => 1]]],
                    ['readConcern' => $this->createDefaultReadConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('readConcern', $event['started']->getCommand());
            }
        );
    }

    public function testDefaultWriteConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Aggregate(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['$out' => $this->getCollectionName() . '.output']],
                    ['writeConcern' => $this->createDefaultWriteConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
            }
        );

        $outCollection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName() . '.output');
        $outCollection->drop();
    }

    public function testEmptyPipelineReturnsAllDocuments(): void
    {
        $this->createFixtures(3);

        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), []);
        $results = iterator_to_array($operation->execute($this->getPrimaryServer()));

        $expectedDocuments = [
            (object) ['_id' => 1, 'x' => (object) ['foo' => 'bar']],
            (object) ['_id' => 2, 'x' => (object) ['foo' => 'bar']],
            (object) ['_id' => 3, 'x' => (object) ['foo' => 'bar']],
        ];

        $this->assertEquals($expectedDocuments, $results);
    }

    public function testUnrecognizedPipelineState(): void
    {
        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), [['$foo' => 1]]);
        $this->expectException(RuntimeException::class);
        $operation->execute($this->getPrimaryServer());
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Aggregate(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [],
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
     * @dataProvider provideTypeMapOptionsAndExpectedDocuments
     */
    public function testTypeMapOption(?array $typeMap, array $expectedDocuments): void
    {
        $this->createFixtures(3);

        $pipeline = [['$match' => ['_id' => ['$ne' => 2]]]];

        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), $pipeline, ['typeMap' => $typeMap]);
        $results = iterator_to_array($operation->execute($this->getPrimaryServer()));

        $this->assertEquals($expectedDocuments, $results);
    }

    public function testExplainOption(): void
    {
        $this->createFixtures(3);

        $pipeline = [['$match' => ['_id' => ['$ne' => 2]]]];
        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), $pipeline, ['explain' => true, 'typeMap' => ['root' => 'array']]);
        $results = iterator_to_array($operation->execute($this->getPrimaryServer()));

        $this->assertCount(1, $results);

        /* MongoDB 4.2 may optimize aggregate pipelines into queries, which can
         * result in different explain output (see: SERVER-24860) */
        $this->assertThat($results[0], $this->logicalOr(
            $this->arrayHasKey('stages'),
            $this->arrayHasKey('queryPlanner')
        ));
    }

    public function testExplainOptionWithWriteConcern(): void
    {
        $this->createFixtures(3);

        $pipeline = [['$match' => ['_id' => ['$ne' => 2]]], ['$out' => $this->getCollectionName() . '.output']];
        $options = ['explain' => true, 'writeConcern' => new WriteConcern(1)];

        (new CommandObserver())->observe(
            function () use ($pipeline, $options): void {
                $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), $pipeline, $options);

                $results = iterator_to_array($operation->execute($this->getPrimaryServer()));

                $this->assertCount(1, $results);
                $result = current($results);

                if (isset($result->shards)) {
                    foreach ($result->shards as $shard) {
                        $this->assertObjectHasAttribute('stages', $shard);
                    }
                } else {
                    $this->assertObjectHasAttribute('stages', $result);
                }
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
            }
        );

        $this->assertCollectionCount($this->getCollectionName() . '.output', 0);
    }

    public function testBypassDocumentValidationSetWhenTrue(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Aggregate(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['$match' => ['x' => 1]]],
                    ['bypassDocumentValidation' => true]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
                $this->assertEquals(true, $event['started']->getCommand()->bypassDocumentValidation);
            }
        );
    }

    public function testBypassDocumentValidationUnsetWhenFalse(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Aggregate(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['$match' => ['x' => 1]]],
                    ['bypassDocumentValidation' => false]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
            }
        );
    }

    public function provideTypeMapOptionsAndExpectedDocuments()
    {
        return [
            [
                null,
                [
                    (object) ['_id' => 1, 'x' => (object) ['foo' => 'bar']],
                    (object) ['_id' => 3, 'x' => (object) ['foo' => 'bar']],
                ],
            ],
            [
                ['root' => 'array', 'document' => 'array'],
                [
                    ['_id' => 1, 'x' => ['foo' => 'bar']],
                    ['_id' => 3, 'x' => ['foo' => 'bar']],
                ],
            ],
            [
                ['root' => 'object', 'document' => 'array'],
                [
                    (object) ['_id' => 1, 'x' => ['foo' => 'bar']],
                    (object) ['_id' => 3, 'x' => ['foo' => 'bar']],
                ],
            ],
            [
                ['root' => 'array', 'document' => 'stdClass'],
                [
                    ['_id' => 1, 'x' => (object) ['foo' => 'bar']],
                    ['_id' => 3, 'x' => (object) ['foo' => 'bar']],
                ],
            ],
            [
                ['root' => 'array', 'document' => 'stdClass', 'fieldPaths' => ['x' => 'array']],
                [
                    ['_id' => 1, 'x' => ['foo' => 'bar']],
                    ['_id' => 3, 'x' => ['foo' => 'bar']],
                ],
            ],
        ];
    }

    public function testReadPreferenceWithinTransaction(): void
    {
        $this->skipIfTransactionsAreNotSupported();

        // Collection must be created before the transaction starts
        $this->createCollection();

        $session = $this->manager->startSession();
        $session->startTransaction();

        try {
            $this->createFixtures(3, ['session' => $session]);

            $pipeline = [['$match' => ['_id' => ['$lt' => 3]]]];
            $options = [
                'readPreference' => new ReadPreference('primary'),
                'session' => $session,
            ];

            $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), $pipeline, $options);
            $cursor = $operation->execute($this->getPrimaryServer());

            $expected = [
                ['_id' => 1, 'x' => ['foo' => 'bar']],
                ['_id' => 2, 'x' => ['foo' => 'bar']],
            ];

            $this->assertSameDocuments($expected, $cursor);

            $session->commitTransaction();
        } finally {
            $session->endSession();
        }
    }

    /**
     * Create data fixtures.
     */
    private function createFixtures(int $n, array $executeBulkWriteOptions = []): void
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert([
                '_id' => $i,
                'x' => (object) ['foo' => 'bar'],
            ]);
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite, $executeBulkWriteOptions);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
