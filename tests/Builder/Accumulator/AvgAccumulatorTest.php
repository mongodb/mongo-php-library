<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $avg accumulator
 */
class AvgAccumulatorTest extends PipelineTestCase
{
    public function testUseInGroupStage(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::fieldPath('item'),
                avgAmount: Accumulator::avg(
                    Expression::multiply(
                        Expression::numberFieldPath('price'),
                        Expression::intFieldPath('quantity'),
                    ),
                ),
                avgQuantity: Accumulator::avg(
                    Expression::intFieldPath('quantity'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AvgUseInGroupStage, $pipeline);
    }

    public function testUseInSetWindowFieldsStage(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::fieldPath('state'),
                sortBy: object(
                    orderDate: Sort::Asc,
                ),
                output: object(
                    averageQuantityForState: Accumulator::outputWindow(
                        Accumulator::avg(
                            Expression::intFieldPath('quantity'),
                        ),
                        documents: ['unbounded', 'current'],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AvgUseInSetWindowFieldsStage, $pipeline);
    }
}
