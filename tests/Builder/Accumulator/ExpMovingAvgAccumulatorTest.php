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
 * Test $expMovingAvg accumulator
 */
class ExpMovingAvgAccumulatorTest extends PipelineTestCase
{
    public function testExponentialMovingAverageUsingAlpha(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('stock'),
                sortBy: object(
                    date: Sort::Asc,
                ),
                output: object(
                    expMovingAvgForStock: Accumulator::expMovingAvg(
                        input: Expression::numberFieldPath('price'),
                        alpha: 0.75,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ExpMovingAvgExponentialMovingAverageUsingAlpha, $pipeline);
    }

    public function testExponentialMovingAverageUsingN(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('stock'),
                sortBy: object(
                    date: Sort::Asc,
                ),
                output: object(
                    expMovingAvgForStock: Accumulator::expMovingAvg(
                        input: Expression::numberFieldPath('price'),
                        N: 2,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ExpMovingAvgExponentialMovingAverageUsingN, $pipeline);
    }
}
