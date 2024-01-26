<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
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
                    timeStamp: 1,
                ),
                output: object(
                    truckAverageSpeed: Accumulator::outputWindow(
                        Accumulator::derivative(
                            input: Expression::numberFieldPath('miles'),
                            unit: 'hour',
                        ),
                        range: [-30, 0],
                        unit: 'second',
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
