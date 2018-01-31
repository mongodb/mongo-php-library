<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Javascript;
use MongoDB\Driver\BulkWrite;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\DropCollection;
use MongoDB\Operation\Find;
use MongoDB\Operation\MapReduce;
use MongoDB\Tests\CommandObserver;
use stdClass;

class MapReduceFunctionalTest extends FunctionalTestCase
{
    public function testDefaultReadConcernIsOmitted()
    {
        $operation = new CreateCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($this->getPrimaryServer());

        (new CommandObserver)->observe(
            function() {
                $operation = new MapReduce(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    new Javascript('function() { emit(this.x, this.y); }'),
                    new Javascript('function(key, values) { return Array.sum(values); }'),
                    ['inline' => 1],
                    ['readConcern' => $this->createDefaultReadConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(stdClass $command) {
                $this->assertObjectNotHasAttribute('readConcern', $command);
            }
        );
    }

    public function testDefaultWriteConcernIsOmitted()
    {
        $operation = new CreateCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($this->getPrimaryServer());

        (new CommandObserver)->observe(
            function() {
                $operation = new MapReduce(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    new Javascript('function() { emit(this.x, this.y); }'),
                    new Javascript('function(key, values) { return Array.sum(values); }'),
                    $this->getCollectionName() . '.output',
                    ['writeConcern' => $this->createDefaultWriteConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(stdClass $command) {
                $this->assertObjectNotHasAttribute('writeConcern', $command);
            }
        );

        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName() . '.output');
        $operation->execute($this->getPrimaryServer());
    }

    public function testFinalize()
    {
        $this->createFixtures(3);

        $map = new Javascript('function() { emit(this.x, this.y); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');
        $out = ['inline' => 1];
        $finalize = new Javascript('function(key, reducedValue) { return reducedValue; }');

        $operation = new MapReduce($this->getDatabaseName(), $this->getCollectionName(), $map, $reduce, $out, ['finalize' => $finalize]);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertNotNull($result);
    }

    public function testResult()
    {
        $this->createFixtures(3);

        $map = new Javascript('function() { emit(this.x, this.y); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');
        $out = ['inline' => 1];

        $operation = new MapReduce($this->getDatabaseName(), $this->getCollectionName(), $map, $reduce, $out);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf('MongoDB\MapReduceResult', $result);
        $this->assertGreaterThanOrEqual(0, $result->getExecutionTimeMS());
        $this->assertNotEmpty($result->getCounts());
    }

    public function testResultIncludesTimingWithVerboseOption()
    {
        $this->createFixtures(3);

        $map = new Javascript('function() { emit(this.x, this.y); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');
        $out = ['inline' => 1];

        $operation = new MapReduce($this->getDatabaseName(), $this->getCollectionName(), $map, $reduce, $out, ['verbose' => true]);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf('MongoDB\MapReduceResult', $result);
        $this->assertGreaterThanOrEqual(0, $result->getExecutionTimeMS());
        $this->assertNotEmpty($result->getCounts());
        $this->assertNotEmpty($result->getTiming());
    }

    public function testResultDoesNotIncludeTimingWithoutVerboseOption()
    {
        $this->createFixtures(3);

        $map = new Javascript('function() { emit(this.x, this.y); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');
        $out = ['inline' => 1];

        $operation = new MapReduce($this->getDatabaseName(), $this->getCollectionName(), $map, $reduce, $out, ['verbose' => false]);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf('MongoDB\MapReduceResult', $result);
        $this->assertGreaterThanOrEqual(0, $result->getExecutionTimeMS());
        $this->assertNotEmpty($result->getCounts());
        $this->assertEmpty($result->getTiming());
    }

    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        $this->createFixtures(3);

        (new CommandObserver)->observe(
            function() {
                $operation = new MapReduce(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    new Javascript('function() { emit(this.x, this.y); }'),
                    new Javascript('function(key, values) { return Array.sum(values); }'),
                    ['inline' => 1],
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
     * @dataProvider provideTypeMapOptionsAndExpectedDocuments
     */
    public function testTypeMapOptionWithInlineResults(array $typeMap = null, array $expectedDocuments)
    {
        $this->createFixtures(3);

        $map = new Javascript('function() { emit(this.x, this.y); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');
        $out = ['inline' => 1];

        $operation = new MapReduce($this->getDatabaseName(), $this->getCollectionName(), $map, $reduce, $out, ['typeMap' => $typeMap]);
        $results = iterator_to_array($operation->execute($this->getPrimaryServer()));

        $this->assertEquals($expectedDocuments, $results);
    }

    public function provideTypeMapOptionsAndExpectedDocuments()
    {
        return [
            [
                null,
                [
                    (object) ['_id' => 1, 'value' => 3],
                    (object) ['_id' => 2, 'value' => 6],
                    (object) ['_id' => 3, 'value' => 9],
                ],
            ],
            [
                ['root' => 'array'],
                [
                    ['_id' => 1, 'value' => 3],
                    ['_id' => 2, 'value' => 6],
                    ['_id' => 3, 'value' => 9],
                ],
            ],
            [
                ['root' => 'object'],
                [
                    (object) ['_id' => 1, 'value' => 3],
                    (object) ['_id' => 2, 'value' => 6],
                    (object) ['_id' => 3, 'value' => 9],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideTypeMapOptionsAndExpectedDocuments
     */
    public function testTypeMapOptionWithOutputCollection(array $typeMap = null, array $expectedDocuments)
    {
        $this->createFixtures(3);

        $map = new Javascript('function() { emit(this.x, this.y); }');
        $reduce = new Javascript('function(key, values) { return Array.sum(values); }');
        $out = $this->getCollectionName() . '.output';

        $operation = new MapReduce($this->getDatabaseName(), $this->getCollectionName(), $map, $reduce, $out, ['typeMap' => $typeMap]);
        $results = iterator_to_array($operation->execute($this->getPrimaryServer()));

        $this->assertEquals($expectedDocuments, $results);

        $operation = new Find($this->getDatabaseName(), $out, [], ['typeMap' => $typeMap]);
        $cursor = $operation->execute($this->getPrimaryServer());

        $this->assertEquals($expectedDocuments, iterator_to_array($cursor));

        $operation = new DropCollection($this->getDatabaseName(), $out);
        $operation->execute($this->getPrimaryServer());
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
            $bulkWrite->insert(['x' => $i, 'y' => $i]);
            $bulkWrite->insert(['x' => $i, 'y' => $i * 2]);
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $this->assertEquals($n * 2, $result->getInsertedCount());
    }
}
