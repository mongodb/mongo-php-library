<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Count;
use MongoDB\Operation\CreateIndexes;
use MongoDB\Operation\Explain;
use MongoDB\Operation\InsertMany;
use MongoDB\Tests\CommandObserver;
use stdClass;

class CountFunctionalTest extends FunctionalTestCase
{
    public function testDefaultReadConcernIsOmitted()
    {
        (new CommandObserver)->observe(
            function() {
                $operation = new Count(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [],
                    ['readConcern' => $this->createDefaultReadConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(stdClass $command) {
                $this->assertObjectNotHasAttribute('readConcern', $command);
            }
        );
    }

    public function testExplainAllPlansExecution()
    {
        $insertMany = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [
            ['x' => 0],
            ['x' => 1],
            ['x' => 2],
            ['y' => 3]
        ]);
        $insertMany->execute($this->getPrimaryServer());

        $operation = new Count($this->getDatabaseName(), $this->getCollectionName(), ['x' => ['$gte' => 1]], []);
        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_ALL_PLANS, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertTrue(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testExplainDefaultVerbosity()
    {
        $insertMany = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [
            ['x' => 0],
            ['x' => 1],
            ['x' => 2],
            ['y' => 3]
        ]);
        $insertMany->execute($this->getPrimaryServer());

        $operation = new Count($this->getDatabaseName(), $this->getCollectionName(), ['x' => ['$gte' => 1]], []);
        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertTrue(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testExplainExecutionStats()
    {
        $insertMany = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [
            ['x' => 0],
            ['x' => 1],
            ['x' => 2],
            ['y' => 3]
        ]);
        $insertMany->execute($this->getPrimaryServer());

        $operation = new Count($this->getDatabaseName(), $this->getCollectionName(), ['x' => ['$gte' => 1]], []);
        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_EXEC_STATS, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));

        $this->assertFalse(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testExplainQueryPlanner()
    {
        $insertMany = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [
            ['x' => 0],
            ['x' => 1],
            ['x' => 2],
            ['y' => 3]
        ]);
        $insertMany->execute($this->getPrimaryServer());

        $operation = new Count($this->getDatabaseName(), $this->getCollectionName(), ['x' => ['$gte' => 1]], []);
        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_QUERY, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertFalse(array_key_exists('executionStats', $result));
    }

    public function testHintOption()
    {
        $insertMany = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [
            ['x' => 1],
            ['x' => 2],
            ['y' => 3],
        ]);
        $insertMany->execute($this->getPrimaryServer());

        $createIndexes = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [
            ['key' => ['x' => 1], 'sparse' => true, 'name' => 'sparse_x'],
            ['key' => ['y' => 1]],
        ]);
        $createIndexes->execute($this->getPrimaryServer());

        $hintsUsingSparseIndex = [
            ['x' => 1],
            'sparse_x',
        ];

        /* Per SERVER-22041, the count command in server versions before 3.3.2
         * may ignore the hint option if its query predicate is empty. */
        $filter = ['_id' => ['$exists' => true]];

        foreach ($hintsUsingSparseIndex as $hint) {
            $operation = new Count($this->getDatabaseName(), $this->getCollectionName(), $filter, ['hint' => $hint]);
            $this->assertEquals(2, $operation->execute($this->getPrimaryServer()));
        }

        $hintsNotUsingSparseIndex = [
            ['_id' => 1],
            ['y' => 1],
            'y_1',
        ];

        foreach ($hintsNotUsingSparseIndex as $hint) {
            $operation = new Count($this->getDatabaseName(), $this->getCollectionName(), $filter, ['hint' => $hint]);
            $this->assertEquals(3, $operation->execute($this->getPrimaryServer()));
        }
    }

    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver)->observe(
            function() {
                $operation = new Count(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [],
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(stdClass $command) {
                $this->assertObjectHasAttribute('lsid', $command);
            }
        );
    }
}
