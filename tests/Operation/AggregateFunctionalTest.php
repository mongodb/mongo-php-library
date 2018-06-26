<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Operation\Aggregate;
use MongoDB\Tests\CommandObserver;
use ArrayIterator;
use stdClass;

class AggregateFunctionalTest extends FunctionalTestCase
{
    public function testCurrentOpCommand()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('$currentOp is not supported');
        }

        (new CommandObserver)->observe(
            function() {
                $operation = new Aggregate(
                    'admin',
                    null,
                    [['$currentOp' => (object) []]]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(array $event) {
                $this->assertSame(1, $event['started']->getCommand()->aggregate);
            }
        );
    }

    public function testDefaultReadConcernIsOmitted()
    {
        (new CommandObserver)->observe(
            function() {
                $operation = new Aggregate(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['$match' => ['x' => 1]]],
                    ['readConcern' => $this->createDefaultReadConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(array $event) {
                $this->assertObjectNotHasAttribute('readConcern', $event['started']->getCommand());
            }
        );
    }

    public function testDefaultWriteConcernIsOmitted()
    {
        (new CommandObserver)->observe(
            function() {
                $operation = new Aggregate(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['$out' => $this->getCollectionName() . '.output']],
                    ['writeConcern' => $this->createDefaultWriteConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(array $event) {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
            }
        );
    }

    public function testEmptyPipelineReturnsAllDocuments()
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

    public function testUnrecognizedPipelineState()
    {
        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), [['$foo' => 1]]);
        $this->expectException(RuntimeException::class);
        $operation->execute($this->getPrimaryServer());
    }

    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver)->observe(
            function() {
                $operation = new Aggregate(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [],
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(array $event) {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }

    /**
     * @dataProvider provideTypeMapOptionsAndExpectedDocuments
     */
    public function testTypeMapOption(array $typeMap = null, array $expectedDocuments)
    {
        $this->createFixtures(3);

        $pipeline = [['$match' => ['_id' => ['$ne' => 2]]]];

        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), $pipeline, ['typeMap' => $typeMap]);
        $results = iterator_to_array($operation->execute($this->getPrimaryServer()));

        $this->assertEquals($expectedDocuments, $results);
    }

    /**
     * @dataProvider provideTypeMapOptionsAndExpectedDocuments
     */
    public function testTypeMapOptionWithoutCursor(array $typeMap = null, array $expectedDocuments)
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '>=')) {
            $this->markTestSkipped('Aggregations with useCursor == false are not supported');
        }

        $this->createFixtures(3);

        $pipeline = [['$match' => ['_id' => ['$ne' => 2]]]];

        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), $pipeline, ['typeMap' => $typeMap, 'useCursor' => false]);
        $results = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(ArrayIterator::class, $results);
        $this->assertEquals($expectedDocuments, iterator_to_array($results));
    }

    public function testExplainOption()
    {
        $this->createFixtures(3);

        $pipeline = [['$match' => ['_id' => ['$ne' => 2]]]];
        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), $pipeline, ['explain' => true, 'typeMap' => ['root' => 'array']]);
        $results = iterator_to_array($operation->execute($this->getPrimaryServer()));

        $this->assertCount(1, $results);
        $this->assertArrayHasKey('stages', $results[0]);
    }

    public function testExplainOptionWithWriteConcern()
    {
        if (version_compare($this->getServerVersion(), '3.4.0', '<')) {
            $this->markTestSkipped('The writeConcern option is not supported');
       }

        $this->createFixtures(3);

        $pipeline = [['$match' => ['_id' => ['$ne' => 2]]], ['$out' => $this->getCollectionName() . '.output']];
        $options = ['explain' => true, 'writeConcern' => new WriteConcern(1)];

        (new CommandObserver)->observe(
            function() use ($pipeline, $options) {
                $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), $pipeline, $options);

                $results = iterator_to_array($operation->execute($this->getPrimaryServer()));

                $this->assertCount(1, $results);
                $this->assertObjectHasAttribute('stages', current($results));
            },
            function(array $event) {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
            }
        );

        $this->assertCollectionCount($this->getCollectionName() . '.output', 0);
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
        ];
    }

    /**
     * Create data fixtures.
     *
     * @param integer $n
     */
    private function createFixtures($n)
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert([
                '_id' => $i,
                'x' => (object) ['foo' => 'bar'],
            ]);
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
