<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

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
                _id: Sort::Asc,
                store: Sort::Asc,
                item: Sort::Asc,
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
                total: Sort::Desc,
            ),
        );

        $this->assertSamePipeline(Pipelines::UnionWithReport2AggregatedSalesByItems, $pipeline);
    }
}
