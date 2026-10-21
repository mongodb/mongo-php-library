<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $minMaxScaler accumulator
 */
class MinMaxScalerAccumulatorTest extends PipelineTestCase
{
    public function testNormalizeValuesWithCustomRange(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                sortBy: object(a: 1),
                output: object(
                    scaled: Accumulator::minMaxScaler(
                        input: Expression::numberFieldPath('a'),
                    ),
                    scaledTo100: Accumulator::minMaxScaler(
                        input: Expression::numberFieldPath('a'),
                        min: 0,
                        max: 100,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MinMaxScalerNormalizeValuesWithCustomRange, $pipeline);
    }
}
