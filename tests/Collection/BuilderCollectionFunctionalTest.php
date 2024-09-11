<?php

namespace MongoDB\Tests\Collection;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;

class BuilderCollectionFunctionalTest extends FunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->collection->insertMany([['x' => 1], ['x' => 2], ['x' => 2]]);
    }

    public function testAggregate(): void
    {
        $this->markTestSkipped('Not supported yet');
    }

    public function testBulkWriteDeleteMany(): void
    {
        $result = $this->collection->bulkWrite([
            [
                'deleteMany' => [
                    Query::query(x: Query::gt(1)),
                ],
            ],
        ]);
        $this->assertEquals(2, $result->getDeletedCount());
    }

    public function testBulkWriteDeleteOne(): void
    {
        $result = $this->collection->bulkWrite([
            [
                'deleteOne' => [
                    Query::query(x: Query::eq(1)),
                ],
            ],
        ]);
        $this->assertEquals(1, $result->getDeletedCount());
    }

    public function testBulkWriteReplaceOne(): void
    {
        $result = $this->collection->bulkWrite([
            [
                'replaceOne' => [
                    Query::query(x: Query::eq(1)),
                    ['x' => 3],
                ],
            ],
        ]);
        $this->assertEquals(1, $result->getModifiedCount());

        $result = $this->collection->findOne(Query::query(x: Query::eq(3)));
        $this->assertEquals(3, $result->x);
    }

    public function testBulkWriteUpdateMany(): void
    {
        $result = $this->collection->bulkWrite([
            [
                'updateMany' => [
                    Query::query(x: Query::gt(1)),
                    // @todo Use Builder when update operators are supported by PHPLIB-1507
                    ['$set' => ['x' => 3]],
                ],
            ],
        ]);
        $this->assertEquals(2, $result->getModifiedCount());

        $result = $this->collection->find(Query::query(x: Query::eq(3)))->toArray();
        $this->assertCount(2, $result);
        $this->assertEquals(3, $result[0]->x);
    }

    public function testBulkWriteUpdateOne(): void
    {
        $result = $this->collection->bulkWrite([
            [
                'updateOne' => [
                    Query::query(x: Query::eq(1)),
                    // @todo Use Builder when update operators are supported by PHPLIB-1507
                    ['$set' => ['x' => 3]],
                ],
            ],
        ]);

        $this->assertEquals(1, $result->getModifiedCount());

        $result = $this->collection->findOne(Query::query(x: Query::eq(3)));
        $this->assertEquals(3, $result->x);
    }

    public function testCountDocuments(): void
    {
        $result = $this->collection->countDocuments(Query::query(x: Query::gt(1)));
        $this->assertEquals(2, $result);
    }

    public function testDeleteMany(): void
    {
        $result = $this->collection->deleteMany(Query::query(x: Query::gt(1)));
        $this->assertEquals(2, $result->getDeletedCount());
    }

    public function testDeleteOne(): void
    {
        $result = $this->collection->deleteOne(Query::query(x: Query::gt(1)));
        $this->assertEquals(1, $result->getDeletedCount());
    }

    public function testDistinct(): void
    {
        $result = $this->collection->distinct('x', Query::query(x: Query::gt(1)));
        $this->assertEquals([2], $result);
    }

    public function testFind(): void
    {
        $results = $this->collection->find(Query::query(x: Query::gt(1)))->toArray();
        $this->assertCount(2, $results);
        $this->assertEquals(2, $results[0]->x);
    }

    public function testFindOne(): void
    {
        $result = $this->collection->findOne(Query::query(x: Query::eq(1)));
        $this->assertEquals(1, $result->x);
    }

    public function testFindOneAndDelete(): void
    {
        $result = $this->collection->findOneAndDelete(Query::query(x: Query::eq(1)));
        $this->assertEquals(1, $result->x);

        $result = $this->collection->find()->toArray();
        $this->assertCount(2, $result);
    }

    public function testFindOneAndReplace(): void
    {
        $this->collection->insertOne(['x' => 1]);

        $result = $this->collection->findOneAndReplace(
            Query::query(x: Query::lt(2)),
            ['x' => 3],
        );
        $this->assertEquals(1, $result->x);

        $result = $this->collection->findOne(Query::query(x: Query::eq(3)));
        $this->assertEquals(3, $result->x);
    }

    public function testFindOneAndUpdate(): void
    {
        $result = $this->collection->findOneAndUpdate(
            Query::query(x: Query::lt(2)),
            // @todo Use Builder when update operators are supported by PHPLIB-1507
            ['$set' => ['x' => 3]],
        );
        $this->assertEquals(1, $result->x);

        $result = $this->collection->findOne(Query::query(x: Query::eq(3)));
        $this->assertEquals(3, $result->x);
    }

    public function testReplaceOne(): void
    {
        $this->collection->insertOne(['x' => 1]);

        $result = $this->collection->replaceOne(
            Query::query(x: Query::lt(2)),
            ['x' => 3],
        );
        $this->assertEquals(1, $result->getModifiedCount());

        $result = $this->collection->findOne(Query::query(x: Query::eq(3)));
        $this->assertEquals(3, $result->x);
    }

    public function testUpdateOne(): void
    {
        $this->collection->insertOne(['x' => 1]);

        $result = $this->collection->updateOne(
            Query::query(x: Query::lt(2)),
            // @todo Use Builder when update operators are supported by PHPLIB-1507
            ['$set' => ['x' => 3]],
        );
        $this->assertEquals(1, $result->getModifiedCount());

        $result = $this->collection->findOne(Query::query(x: Query::eq(3)));
        $this->assertEquals(3, $result->x);
    }

    public function testUpdateWithPipeline(): void
    {
        $this->skipIfServerVersion('<', '4.2.0', 'Pipeline-style updates are not supported');

        $result = $this->collection->updateOne(
            Query::query(x: Query::lt(2)),
            new Pipeline(
                Stage::set(x: 3),
            ),
        );

        $this->assertEquals(1, $result->getModifiedCount());
    }

    public function testUpdateMany(): void
    {
        $result = $this->collection->updateMany(
            Query::query(x: Query::gt(1)),
            // @todo Use Builder when update operators are supported by PHPLIB-1507
            ['$set' => ['x' => 3]],
        );
        $this->assertEquals(2, $result->getModifiedCount());

        $result = $this->collection->find(Query::query(x: Query::eq(3)))->toArray();
        $this->assertCount(2, $result);
        $this->assertEquals(3, $result[0]->x);
    }

    public function testUpdateManyWithPipeline(): void
    {
        $this->skipIfServerVersion('<', '4.2.0', 'Pipeline-style updates are not supported');

        $result = $this->collection->updateMany(
            Query::query(x: Query::gt(1)),
            new Pipeline(
                Stage::set(x: 3),
            ),
        );
        $this->assertEquals(2, $result->getModifiedCount());

        $result = $this->collection->find(Query::query(x: Query::eq(3)))->toArray();
        $this->assertCount(2, $result);
        $this->assertEquals(3, $result[0]->x);
    }

    public function testWatch(): void
    {
        $this->markTestSkipped('Not supported yet');
    }
}
