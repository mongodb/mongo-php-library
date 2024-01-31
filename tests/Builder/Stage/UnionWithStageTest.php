<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $unionWith stage
 */
class UnionWithStageTest extends PipelineTestCase
{
    public function testReport1AllSalesByYearAndStoresAndItems(): void
    {
        $pipeline = new Pipeline(
            Stage::set(
                _id: '2017',
            ),
            Stage::unionWith(
                coll: 'sales_2018',
                pipeline: new Pipeline(
                    Stage::set(
                        _id: '2018',
                    ),
                ),
            ),
            Stage::unionWith(
                coll: 'sales_2019',
                pipeline: new Pipeline(
                    Stage::set(
                        _id: '2019',
                    ),
                ),
            ),
            Stage::unionWith(
                coll: 'sales_2020',
                pipeline: new Pipeline(
                    Stage::set(
                        _id: '2020',
                    ),
                ),
            ),
            Stage::sort(
                object(
                    _id: 1,
                    store: 1,
                    item: 1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::UnionWithReport1AllSalesByYearAndStoresAndItems, $pipeline);
    }

    public function testReport2AggregatedSalesByItems(): void
    {
        $pipeline = new Pipeline(
            Stage::unionWith('sales_2018'),
            Stage::unionWith('sales_2019'),
            Stage::unionWith('sales_2020'),
            Stage::group(
                _id: Expression::stringFieldPath('item'),
                total: Accumulator::sum(
                    Expression::numberFieldPath('quantity'),
                ),
            ),
            Stage::sort(
                object(
                    total: -1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::UnionWithReport2AggregatedSalesByItems, $pipeline);
    }
}
