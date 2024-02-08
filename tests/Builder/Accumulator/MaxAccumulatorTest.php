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
 * Test $max accumulator
 */
class MaxAccumulatorTest extends PipelineTestCase
{
    public function testUseInGroupStage(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::fieldPath('item'),
                maxTotalAmount: Accumulator::max(
                    Expression::multiply(
                        Expression::numberFieldPath('price'),
                        Expression::intFieldPath('quantity'),
                    ),
                ),
                maxQuantity: Accumulator::max(
                    Expression::intFieldPath('quantity'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MaxUseInGroupStage, $pipeline);
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
                    maximumQuantityForState: Accumulator::outputWindow(
                        Accumulator::max(
                            Expression::intFieldPath('quantity'),
                        ),
                        documents: ['unbounded', 'current'],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MaxUseInSetWindowFieldsStage, $pipeline);
    }
}
