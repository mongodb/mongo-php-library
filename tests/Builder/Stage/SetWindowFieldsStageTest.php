<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Builder\Type\TimeUnit;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $setWindowFields stage
 */
class SetWindowFieldsStageTest extends PipelineTestCase
{
    public function testRangeWindowExample(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('state'),
                sortBy: object(price: Sort::Asc),
                output: object(
                    quantityFromSimilarOrders: Accumulator::outputWindow(
                        Accumulator::sum(
                            Expression::numberFieldPath('quantity'),
                        ),
                        range: [-10, 10],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetWindowFieldsRangeWindowExample, $pipeline);
    }

    public function testUseATimeRangeWindowWithANegativeUpperBound(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('state'),
                sortBy: object(orderDate: Sort::Asc),
                output: object(
                    recentOrders: Accumulator::outputWindow(
                        Accumulator::push(
                            Expression::dateFieldPath('orderDate'),
                        ),
                        range: ['unbounded', -10],
                        unit: TimeUnit::Month,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetWindowFieldsUseATimeRangeWindowWithANegativeUpperBound, $pipeline);
    }

    public function testUseATimeRangeWindowWithAPositiveUpperBound(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('state'),
                sortBy: object(orderDate: Sort::Asc),
                output: object(
                    recentOrders: Accumulator::outputWindow(
                        Accumulator::push(
                            Expression::dateFieldPath('orderDate'),
                        ),
                        range: ['unbounded', 10],
                        unit: TimeUnit::Month,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetWindowFieldsUseATimeRangeWindowWithAPositiveUpperBound, $pipeline);
    }

    public function testUseDocumentsWindowToObtainCumulativeAndMaximumQuantityForEachYear(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::year(
                    Expression::dateFieldPath('orderDate'),
                ),
                sortBy: object(orderDate: Sort::Asc),
                output: object(
                    cumulativeQuantityForYear: Accumulator::outputWindow(
                        Accumulator::sum(
                            Expression::numberFieldPath('quantity'),
                        ),
                        documents: ['unbounded', 'current'],
                    ),
                    maximumQuantityForYear: Accumulator::outputWindow(
                        Accumulator::max(
                            Expression::numberFieldPath('quantity'),
                        ),
                        documents: ['unbounded', 'unbounded'],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetWindowFieldsUseDocumentsWindowToObtainCumulativeAndMaximumQuantityForEachYear, $pipeline);
    }

    public function testUseDocumentsWindowToObtainCumulativeQuantityForEachState(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('state'),
                sortBy: object(orderDate: Sort::Asc),
                output: object(
                    cumulativeQuantityForState: Accumulator::outputWindow(
                        Accumulator::sum(
                            Expression::numberFieldPath('quantity'),
                        ),
                        documents: ['unbounded', 'current'],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetWindowFieldsUseDocumentsWindowToObtainCumulativeQuantityForEachState, $pipeline);
    }

    public function testUseDocumentsWindowToObtainCumulativeQuantityForEachYear(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::year(
                    Expression::dateFieldPath('orderDate'),
                ),
                sortBy: object(orderDate: Sort::Asc),
                output: object(
                    cumulativeQuantityForYear: Accumulator::outputWindow(
                        Accumulator::sum(
                            Expression::numberFieldPath('quantity'),
                        ),
                        documents: ['unbounded', 'current'],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetWindowFieldsUseDocumentsWindowToObtainCumulativeQuantityForEachYear, $pipeline);
    }

    public function testUseDocumentsWindowToObtainMovingAverageQuantityForEachYear(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::year(
                    Expression::dateFieldPath('orderDate'),
                ),
                sortBy: object(orderDate: Sort::Asc),
                output: object(
                    averageQuantity: Accumulator::outputWindow(
                        Accumulator::avg(
                            Expression::numberFieldPath('quantity'),
                        ),
                        documents: [-1, 0],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetWindowFieldsUseDocumentsWindowToObtainMovingAverageQuantityForEachYear, $pipeline);
    }
}
