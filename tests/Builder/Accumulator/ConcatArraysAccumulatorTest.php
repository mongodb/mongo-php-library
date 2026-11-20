<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $concatArrays accumulator
 */
class ConcatArraysAccumulatorTest extends PipelineTestCase
{
    public function testWarehouseCollection(): void
    {
        $concatArrays = Accumulator::concatArrays(...);

        $pipeline = new Pipeline(
            Stage::project(
                items: $concatArrays(
                    Expression::arrayFieldPath('instock'),
                    Expression::arrayFieldPath('ordered'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ConcatArraysWarehouseCollection, $pipeline);
    }
}
