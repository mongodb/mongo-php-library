<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\BulkWrite;
use MongoDB\Operation\Aggregate;
use MongoDB\Operation\Count;
use MongoDB\Operation\Delete;
use MongoDB\Operation\DeleteMany;
use MongoDB\Operation\DeleteOne;
use MongoDB\Operation\Distinct;
use MongoDB\Operation\Explain;
use MongoDB\Operation\Find;
use MongoDB\Operation\FindAndModify;
use MongoDB\Operation\FindOne;
use MongoDB\Operation\FindOneAndDelete;
use MongoDB\Operation\FindOneAndReplace;
use MongoDB\Operation\FindOneAndUpdate;
use MongoDB\Operation\Update;
use MongoDB\Operation\UpdateMany;
use MongoDB\Operation\UpdateOne;
use MongoDB\Tests\CommandObserver;

use function array_key_exists;
use function array_key_first;

class ExplainFunctionalTest extends FunctionalTestCase
{
    /** @dataProvider provideVerbosityInformation */
    public function testCount($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $this->createFixtures(3);

        $operation = new Count($this->getDatabaseName(), $this->getCollectionName(), ['x' => ['$gte' => 1]], []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /** @dataProvider provideVerbosityInformation */
    public function testDelete($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $this->createFixtures(3);

        $filter = ['_id' => 1];

        $operation = new Delete($this->getDatabaseName(), $this->getCollectionName(), $filter, 1);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /** @dataProvider provideVerbosityInformation */
    public function testDeleteMany($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $this->createFixtures(3);

        $filter = ['_id' => ['$gt' => 1]];

        $operation = new DeleteMany($this->getDatabaseName(), $this->getCollectionName(), $filter);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /** @dataProvider provideVerbosityInformation */
    public function testDeleteOne($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $this->createFixtures(3);

        $filter = ['_id' => 1];

        $operation = new DeleteOne($this->getDatabaseName(), $this->getCollectionName(), $filter);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /** @dataProvider provideVerbosityInformation */
    public function testDistinct($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $operation = new Distinct($this->getDatabaseName(), $this->getCollectionName(), 'x', []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /** @dataProvider provideVerbosityInformation */
    public function testFindAndModify($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $operation = new FindAndModify($this->getDatabaseName(), $this->getCollectionName(), ['remove' => true]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /** @dataProvider provideVerbosityInformation */
    public function testFind($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $this->createFixtures(3);

        $operation = new Find($this->getDatabaseName(), $this->getCollectionName(), []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    public function testFindMaxAwait(): void
    {
        $maxAwaitTimeMS = 100;

        /* Calculate an approximate pivot to use for time assertions. We will
         * assert that the duration of blocking responses is greater than this
         * value, and vice versa. */
        $pivot = $maxAwaitTimeMS * 0.001 * 0.9;

        // Create a capped collection.
        $databaseName = $this->getDatabaseName();
        $cappedCollectionName = $this->getCollectionName();
        $cappedCollectionOptions = [
            'capped' => true,
            'max' => 100,
            'size' => 1_048_576,
        ];

        $this->createCollection($databaseName, $cappedCollectionName, $cappedCollectionOptions);

        $this->createFixtures(2);

        $operation = new Find($databaseName, $cappedCollectionName, [], ['cursorType' => Find::TAILABLE_AWAIT, 'maxAwaitTimeMS' => $maxAwaitTimeMS]);

        (new CommandObserver())->observe(
            function () use ($operation): void {
                $explainOperation = new Explain($this->getDatabaseName(), $operation, ['typeMap' => ['root' => 'array', 'document' => 'array']]);
                $explainOperation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $command = $event['started']->getCommand();
                $this->assertObjectNotHasAttribute('maxAwaitTimeMS', $command->explain);
                $this->assertObjectHasAttribute('tailable', $command->explain);
                $this->assertObjectHasAttribute('awaitData', $command->explain);
            },
        );
    }

    public function testFindModifiers(): void
    {
        $this->createFixtures(3);

        $operation = new Find(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            [],
            ['modifiers' => ['$orderby' => ['_id' => 1]]],
        );

        (new CommandObserver())->observe(
            function () use ($operation): void {
                $explainOperation = new Explain($this->getDatabaseName(), $operation, ['typeMap' => ['root' => 'array', 'document' => 'array']]);
                $explainOperation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $command = $event['started']->getCommand();
                $this->assertObjectHasAttribute('sort', $command->explain);
                $this->assertObjectNotHasAttribute('modifiers', $command->explain);
            },
        );
    }

    /** @dataProvider provideVerbosityInformation */
    public function testFindOne($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $this->createFixtures(1);

        $operation = new FindOne($this->getDatabaseName(), $this->getCollectionName(), []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /** @dataProvider provideVerbosityInformation */
    public function testFindOneAndDelete($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $operation = new FindOneAndDelete($this->getDatabaseName(), $this->getCollectionName(), []);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /** @dataProvider provideVerbosityInformation */
    public function testFindOneAndReplace($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $operation = new FindOneAndReplace($this->getDatabaseName(), $this->getCollectionName(), ['x' => 1.1], ['x' => 5]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /** @dataProvider provideVerbosityInformation */
    public function testFindOneAndUpdate($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $operation = new FindOneAndUpdate($this->getDatabaseName(), $this->getCollectionName(), [], ['$rename' => ['x' => 'y']]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /** @dataProvider provideVerbosityInformation */
    public function testUpdate($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $this->createFixtures(3);

        $filter = ['_id' => ['$gt' => 1]];
        $update = ['$inc' => ['x' => 1]];

        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, $update);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    public function testUpdateBypassDocumentValidationSetWhenTrue(): void
    {
        $this->createFixtures(3);

        (new CommandObserver())->observe(
            function (): void {
                $operation = new Update(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['_id' => ['$gt' => 1]],
                    ['$inc' => ['x' => 1]],
                    ['bypassDocumentValidation' => true],
                );

                $explainOperation = new Explain($this->getDatabaseName(), $operation);
                $result = $explainOperation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute(
                    'bypassDocumentValidation',
                    $event['started']->getCommand()->explain,
                );
                $this->assertEquals(true, $event['started']->getCommand()->explain->bypassDocumentValidation);
            },
        );
    }

    public function testUpdateBypassDocumentValidationUnsetWhenFalse(): void
    {
        $this->createFixtures(3);

        (new CommandObserver())->observe(
            function (): void {
                $operation = new Update(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['_id' => ['$gt' => 1]],
                    ['$inc' => ['x' => 1]],
                    ['bypassDocumentValidation' => false],
                );

                $explainOperation = new Explain($this->getDatabaseName(), $operation);
                $result = $explainOperation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute(
                    'bypassDocumentValidation',
                    $event['started']->getCommand()->explain,
                );
            },
        );
    }

    /** @dataProvider provideVerbosityInformation */
    public function testUpdateMany($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $this->createFixtures(3);

        $filter = ['_id' => ['$gt' => 1]];
        $update = ['$inc' => ['x' => 1]];

        $operation = new UpdateMany($this->getDatabaseName(), $this->getCollectionName(), $filter, $update);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    /** @dataProvider provideVerbosityInformation */
    public function testUpdateOne($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $this->createFixtures(3);

        $filter = ['_id' => ['$lte' => 1]];
        $update = ['$inc' => ['x' => 1]];

        $operation = new UpdateOne($this->getDatabaseName(), $this->getCollectionName(), $filter, $update);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    public function testAggregate(): void
    {
        $this->skipIfServerVersion('<', '4.0.0', 'Explaining aggregate command requires server version >= 4.0');

        $this->createFixtures(3);

        // Use a $sort stage to ensure the aggregate does not get optimised to a query
        $pipeline = [['$group' => ['_id' => null]], ['$sort' => ['_id' => 1]]];
        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), $pipeline);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_QUERY, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, false, false);
    }

    /** @dataProvider provideVerbosityInformation */
    public function testAggregateOptimizedToQuery($verbosity, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $this->skipIfServerVersion('<', '4.2.0', 'MongoDB < 4.2 does not optimize simple aggregation pipelines');

        $this->createFixtures(3);

        $pipeline = [['$match' => ['_id' => ['$ne' => 2]]]];
        $operation = new Aggregate($this->getDatabaseName(), $this->getCollectionName(), $pipeline);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => $verbosity, 'typeMap' => ['root' => 'array', 'document' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected);
    }

    public function provideVerbosityInformation()
    {
        return [
            [Explain::VERBOSITY_ALL_PLANS, true, true],
            [Explain::VERBOSITY_EXEC_STATS, true, false],
            [Explain::VERBOSITY_QUERY, false, false],
        ];
    }

    private function assertExplainResult($result, $executionStatsExpected, $allPlansExecutionExpected): void
    {
        $checkResult = $result;

        if (array_key_exists('shards', $result)) {
            $firstShard = array_key_first($result['shards']);
            $checkResult = $result['shards'][$firstShard];
        }

        $this->assertThat($checkResult, $this->logicalOr(
            $this->arrayHasKey('stages'),
            $this->arrayHasKey('queryPlanner'),
        ));

        if ($executionStatsExpected) {
            $this->assertArrayHasKey('executionStats', $checkResult);
            if ($allPlansExecutionExpected) {
                $this->assertArrayHasKey('allPlansExecution', $checkResult['executionStats']);
            } else {
                $this->assertArrayNotHasKey('allPlansExecution', $checkResult['executionStats']);
            }
        } else {
            $this->assertArrayNotHasKey('executionStats', $checkResult);
        }
    }

    /**
     * Create data fixtures.
     */
    private function createFixtures(int $n): void
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert([
                '_id' => $i,
                'x' => (integer) ($i . $i),
            ]);
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
