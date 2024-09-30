<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Builder\Type\TimeUnit;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $integral accumulator
 */
class IntegralAccumulatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('powerMeterID'),
                sortBy: object(
                    timeStamp: Sort::Asc,
                ),
                output: object(
                    powerMeterKilowattHours: Accumulator::outputWindow(
                        Accumulator::integral(
                            input: Expression::numberFieldPath('kilowatts'),
                            unit: TimeUnit::Hour,
                        ),
                        range: ['unbounded', 'current'],
                        unit: TimeUnit::Hour,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::IntegralExample, $pipeline);
    }
}
