<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $lookup stage
 */
class LookupStageTest extends PipelineTestCase
{
    public function testPerformAConciseCorrelatedSubqueryWithLookup(): void
    {
        $pipeline = new Pipeline(
            Stage::lookup(
                from: 'restaurants',
                localField: 'restaurant_name',
                foreignField: 'name',
                let: object(
                    orders_drink: Expression::fieldPath('drink'),
                ),
                pipeline: new Pipeline(
                    Stage::match(
                        Query::expr(
                            Expression::in(
                                Expression::variable('orders_drink'),
                                Expression::fieldPath('beverages'),
                            ),
                        ),
                    ),
                ),
                as: 'matches',
            ),
        );

        $this->assertSamePipeline(Pipelines::LookupPerformAConciseCorrelatedSubqueryWithLookup, $pipeline);
    }

    public function testPerformASingleEqualityJoinWithLookup(): void
    {
        $pipeline = new Pipeline(
            Stage::lookup(
                from: 'inventory',
                localField: 'item',
                foreignField: 'sku',
                as: 'inventory_docs',
            ),
        );

        $this->assertSamePipeline(Pipelines::LookupPerformASingleEqualityJoinWithLookup, $pipeline);
    }

    public function testPerformAnUncorrelatedSubqueryWithLookup(): void
    {
        $pipeline = new Pipeline(
            Stage::lookup(
                from: 'holidays',
                pipeline: new Pipeline(
                    Stage::match(
                        year: 2018,
                    ),
                    Stage::project(
                        _id: 0,
                        date: object(
                            name: Expression::stringFieldPath('name'),
                            date: Expression::dateFieldPath('date'),
                        ),
                    ),
                    Stage::replaceRoot(Expression::objectFieldPath('date')),
                ),
                as: 'holidays',
            ),
        );

        $this->assertSamePipeline(Pipelines::LookupPerformAnUncorrelatedSubqueryWithLookup, $pipeline);
    }

    public function testPerformMultipleJoinsAndACorrelatedSubqueryWithLookup(): void
    {
        $pipeline = new Pipeline(
            Stage::lookup(
                from: 'warehouses',
                let: object(
                    order_item: Expression::fieldPath('item'),
                    order_qty: Expression::intFieldPath('ordered'),
                ),
                pipeline: new Pipeline(
                    Stage::match(
                        Query::expr(
                            Expression::and(
                                Expression::eq(
                                    Expression::stringFieldPath('stock_item'),
                                    Expression::variable('order_item'),
                                ),
                                Expression::gte(
                                    Expression::intFieldPath('instock'),
                                    Expression::variable('order_qty'),
                                ),
                            ),
                        ),
                    ),
                    Stage::project(
                        stock_item: 0,
                        _id: 0,
                    ),
                ),
                as: 'stockdata',
            ),
        );

        $this->assertSamePipeline(Pipelines::LookupPerformMultipleJoinsAndACorrelatedSubqueryWithLookup, $pipeline);
    }

    public function testUseLookupWithAnArray(): void
    {
        $pipeline = new Pipeline(
            Stage::lookup(
                from: 'members',
                localField: 'enrollmentlist',
                foreignField: 'name',
                as: 'enrollee_info',
            ),
        );

        $this->assertSamePipeline(Pipelines::LookupUseLookupWithAnArray, $pipeline);
    }

    public function testUseLookupWithMergeObjects(): void
    {
        $pipeline = new Pipeline(
            Stage::lookup(
                from: 'items',
                localField: 'item',
                foreignField: 'item',
                as: 'fromItems',
            ),
            Stage::replaceRoot(
                Expression::mergeObjects(
                    Expression::arrayElemAt(
                        Expression::arrayFieldPath('fromItems'),
                        0,
                    ),
                    Expression::variable('ROOT'),
                ),
            ),
            Stage::project(
                fromItems: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::LookupUseLookupWithMergeObjects, $pipeline);
    }
}
