<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Count;
use MongoDB\Operation\Distinct;
use MongoDB\Operation\Explain;
use MongoDB\Operation\FindAndModify;
use MongoDB\Operation\InsertMany;

class ExplainFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();
        if (version_compare($this->getServerVersion(), '3.0.0', '<')) {
            $this->markTestSkipped('Explain is not supported');
        }
    }

    public function testCountAllPlansExecution()
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

    public function testCountDefaultVerbosity()
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

    public function testCountExecutionStats()
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

    public function testCountQueryPlanner()
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

    public function testDistinctAllPlansExecution()
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('Distinct is not supported on servers with version < 3.2');
        }

        $operation = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_ALL_PLANS, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertTrue(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testDistinctDefaultVerbosity()
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('Distinct is not supported on servers with version < 3.2');
        }

        $operation = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertTrue(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testDistinctExecutionStats()
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('Distinct is not supported on servers with version < 3.2');
        }

        $operation = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_EXEC_STATS, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertFalse(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testDistinctQueryPlanner()
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('Distinct is not supported on servers with version < 3.2');
        }

        $operation = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_QUERY, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertFalse(array_key_exists('executionStats', $result));
    }

    public function testFindAndModifyAllPlansExecution()
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('FindAndModify is not supported on servers with version < 3.2');
        }

        $operation = new FindAndModify($this->getDatabaseName(), $this->getCollectionName(), ['remove' => true]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_ALL_PLANS, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertTrue(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testFindAndModifyDefaultVerbosity()
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('FindAndModify is not supported on servers with version < 3.2');
        }

        $operation = new FindAndModify($this->getDatabaseName(), $this->getCollectionName(), ['remove' => true]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertTrue(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testFindAndModifyExecutionStats()
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('FindAndModify is not supported on servers with version < 3.2');
        }

        $operation = new FindAndModify($this->getDatabaseName(), $this->getCollectionName(), ['remove' => true]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_EXEC_STATS, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertFalse(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testFindAndModifyQueryPlanner()
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('FindAndModify is not supported on servers with version < 3.2');
        }

        $operation = new FindAndModify($this->getDatabaseName(), $this->getCollectionName(), ['remove' => true]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_QUERY, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertFalse(array_key_exists('executionStats', $result));
    }
}
