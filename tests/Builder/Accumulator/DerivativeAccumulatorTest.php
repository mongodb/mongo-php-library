<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Builder\Type\TimeUnit;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $derivative accumulator
 */
class DerivativeAccumulatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('truckID'),
                sortBy: object(
                    timeStamp: Sort::Asc,
                ),
                output: object(
                    truckAverageSpeed: Accumulator::outputWindow(
                        Accumulator::derivative(
                            input: Expression::numberFieldPath('miles'),
                            unit: TimeUnit::Hour,
                        ),
                        range: [-30, 0],
                        unit: TimeUnit::Second,
                    ),
                ),
            ),
            Stage::match(
                truckAverageSpeed: Query::gt(50),
            ),
        );

        $this->assertSamePipeline(Pipelines::DerivativeExample, $pipeline);
    }
}
