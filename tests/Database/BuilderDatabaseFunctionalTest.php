<?php

namespace MongoDB\Tests\Database;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use PHPUnit\Framework\Attributes\TestWith;

class BuilderDatabaseFunctionalTest extends FunctionalTestCase
{
    public function tearDown(): void
    {
        $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());

        parent::tearDown();
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function testAggregate(bool $pipelineAsArray): void
    {
        $this->skipIfServerVersion('<', '6.0.0', '$documents stage is not supported');

        $pipeline = [
            Stage::documents([
                ['x' => 1],
                ['x' => 2],
                ['x' => 3],
            ]),
            Stage::bucketAuto(
                groupBy: Expression::intFieldPath('x'),
                buckets: 2,
            ),
        ];

        if (! $pipelineAsArray) {
            $pipeline = new Pipeline(...$pipeline);
        }

        $results = $this->database->aggregate($pipeline)->toArray();
        $this->assertCount(2, $results);
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function testWatch(bool $pipelineAsArray): void
    {
        $this->skipIfChangeStreamIsNotSupported();

        if ($this->isShardedCluster()) {
            $this->markTestSkipped('Test does not apply on sharded clusters: need more than a single getMore call on the change stream.');
        }

        $pipeline = [
            Stage::match(operationType: Query::eq('insert')),
        ];

        if (! $pipelineAsArray) {
            $pipeline = new Pipeline(...$pipeline);
        }

        $changeStream = $this->database->watch($pipeline);
        $this->database->selectCollection($this->getCollectionName())->insertOne(['x' => 3]);
        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertEquals('insert', $changeStream->current()->operationType);
    }
}
