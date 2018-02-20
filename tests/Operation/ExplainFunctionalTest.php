<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\BulkWrite;
use MongoDB\Operation\Count;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\Distinct;
use MongoDB\Operation\Explain;
use MongoDB\Operation\Find;
use MongoDB\Operation\FindAndModify;
use MongoDB\Operation\FindOne;
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

    /**
     * @dataProvider provideVerbosityInformation
     */
    public function testCount($verbosity, $executionStatsExpected, $allPlansExecutionExpected)
    {
        $insertMany = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [
            ['x' => 0],
            ['x' => 1],
            ['x' => 2],
            ['y' => 3]
        ]);
        $insertMany->execute($this->getPrimaryServer());

        $operation = new Count($this->getDatabaseName(), $this->getCollectionName(), ['x' => ['$gte' => 1]], []);
        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /**
     * @dataProvider provideVerbosityInformation
     */
    public function testDistinct($verbosity, $executionStatsExpected, $allPlansExecutionExpected)
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('Distinct is not supported on servers with version < 3.2');
        }

        $operation = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /**
     * @dataProvider provideVerbosityInformation
     */
    public function testFindAndModify($verbosity, $executionStatsExpected, $allPlansExecutionExpected)
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('FindAndModify is not supported on servers with version < 3.2');
        }

        $operation = new FindAndModify($this->getDatabaseName(), $this->getCollectionName(), ['remove' => true]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /**
     * @dataProvider provideVerbosityInformation
     */
    public function testFind($verbosity, $executionStatsExpected, $allPlansExecutionExpected)
    {
        $this->createFixtures(3);

        $operation = new Find($this->getDatabaseName(), $this->getCollectionName(), [], ['readConcern' => $this->createDefaultReadConcern()]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /**
     * @dataProvider provideVerbosityInformation
     */
    public function testFindMaxAwait($verbosity, $executionStatsExpected, $allPlansExecutionExpected)
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('maxAwaitTimeMS option is not supported');
        }

        $maxAwaitTimeMS = 100;

        /* Calculate an approximate pivot to use for time assertions. We will
         * assert that the duration of blocking responses is greater than this
         * value, and vice versa. */
        $pivot = ($maxAwaitTimeMS * 0.001) * 0.9;

        // Create a capped collection.
        $databaseName = $this->getDatabaseName();
        $cappedCollectionName = $this->getCollectionName();
        $cappedCollectionOptions = [
            'capped' => true,
            'max' => 100,
            'size' => 1048576,
        ];

        $operation = new CreateCollection($databaseName, $cappedCollectionName, $cappedCollectionOptions);
        $operation->execute($this->getPrimaryServer());

        // Insert documents into the capped collection.
        $bulkWrite = new BulkWrite(['ordered' => true]);
        $bulkWrite->insert(['_id' => 1]);
        $bulkWrite->insert(['_id' => 2]);
        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $operation = new Find($databaseName, $cappedCollectionName, [], ['cursorType' => Find::TAILABLE_AWAIT, 'maxAwaitTimeMS' => $maxAwaitTimeMS]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /**
     * @dataProvider provideVerbosityInformation
     */
    public function testFindOne($verbosity, $executionStatsExpected, $allPlansExecutionExpected)
    {
        $this->createFixtures(1);

        $operation = new FindOne($this->getDatabaseName(), $this->getCollectionName(), []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    public function provideVerbosityInformation()
    {
        return [
            [Explain::VERBOSITY_ALL_PLANS, true, true],
            [Explain::VERBOSITY_EXEC_STATS, true, false],
            [Explain::VERBOSITY_QUERY, false, false]
        ];
    }

    private function assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected)
    {
        $this->assertArrayHasKey('queryPlanner', $result);
        if ($executionStatsExpected) {
            $this->assertArrayHasKey('executionStats', $result);
            if ($allPlansExecutionExpected) {
                $this->assertArrayHasKey('allPlansExecution', $result['executionStats']);
            } else {
                $this->assertArrayNotHasKey('allPlansExecution', $result['executionStats']);
            }
        } else {
            $this->assertArrayNotHasKey('executionStats', $result);
        }
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
