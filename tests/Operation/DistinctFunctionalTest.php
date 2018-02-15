<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Distinct;
use MongoDB\Operation\Explain;
use MongoDB\Tests\CommandObserver;
use stdClass;

class DistinctFunctionalTest extends FunctionalTestCase
{
    public function testDefaultReadConcernIsOmitted()
    {
        (new CommandObserver)->observe(
            function() {
                $operation = new Distinct(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    'x',
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
        $operation = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_ALL_PLANS, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertTrue(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testExplainDefaultVerbosity()
    {
        $operation = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertTrue(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testExplainExecutionStats()
    {
        $operation = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_EXEC_STATS, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertFalse(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testExplainQueryPlanner()
    {
        $operation = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_QUERY, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertFalse(array_key_exists('executionStats', $result));
    }

    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver)->observe(
            function() {
                $operation = new Distinct(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    'x',
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
