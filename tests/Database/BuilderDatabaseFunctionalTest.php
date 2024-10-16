<?php

namespace MongoDB\Tests\Database;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;

use function iterator_to_array;

class BuilderDatabaseFunctionalTest extends FunctionalTestCase
{
    public function tearDown(): void
    {
        $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());

        parent::tearDown();
    }

    public function testAggregate(): void
    {
        $this->skipIfServerVersion('<', '6.0.0', '$documents stage is not supported');

        $pipeline = new Pipeline(
            Stage::documents([
                ['x' => 1],
                ['x' => 2],
                ['x' => 3],
            ]),
            Stage::bucketAuto(
                groupBy: Expression::intFieldPath('x'),
                buckets: 2,
            ),
        );
        // Extract the list of stages for arg type restriction
        $pipeline = iterator_to_array($pipeline);

        $results = $this->database->aggregate($pipeline)->toArray();
        $this->assertCount(2, $results);
    }

    public function testWatch(): void
    {
        $this->skipIfChangeStreamIsNotSupported();

        if ($this->isShardedCluster()) {
            $this->markTestSkipped('Test does not apply on sharded clusters: need more than a single getMore call on the change stream.');
        }

        $pipeline = new Pipeline(
            Stage::match(operationType: Query::eq('insert')),
        );
        // Extract the list of stages for arg type restriction
        $pipeline = iterator_to_array($pipeline);

        $changeStream = $this->database->watch($pipeline);
        $this->database->selectCollection($this->getCollectionName())->insertOne(['x' => 3]);
        $changeStream->next();
        $this->assertTrue($changeStream->valid());
        $this->assertEquals('insert', $changeStream->current()->operationType);
    }
}
