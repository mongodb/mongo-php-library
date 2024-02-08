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
 * Test $sum accumulator
 */
class SumAccumulatorTest extends PipelineTestCase
{
    public function testUseInGroupStage(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: object(
                    day: Expression::dayOfYear(
                        Expression::dateFieldPath('date'),
                    ),
                    year: Expression::year(
                        Expression::dateFieldPath('date'),
                    ),
                ),
                totalAmount: Accumulator::sum(
                    Expression::multiply(
                        Expression::numberFieldPath('price'),
                        Expression::intFieldPath('quantity'),
                    ),
                ),
                count: Accumulator::sum(1),
            ),
        );

        $this->assertSamePipeline(Pipelines::SumUseInGroupStage, $pipeline);
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
                    sumQuantityForState: Accumulator::outputWindow(
                        Accumulator::sum(
                            Expression::intFieldPath('quantity'),
                        ),
                        documents: ['unbounded', 'current'],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SumUseInSetWindowFieldsStage, $pipeline);
    }
}
