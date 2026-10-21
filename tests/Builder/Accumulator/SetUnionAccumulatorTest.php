<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $setUnion accumulator
 */
class SetUnionAccumulatorTest extends PipelineTestCase
{
    public function testFlowersCollection(): void
    {
        $setUnion = Accumulator::setUnion(...);

        $pipeline = new Pipeline(
            Stage::project(
                flowerFieldA: 1,
                flowerFieldB: 1,
                allValues: $setUnion(
                    Expression::arrayFieldPath('flowerFieldA'),
                    Expression::arrayFieldPath('flowerFieldB'),
                ),
                _id: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::SetUnionFlowersCollection, $pipeline);
    }
}
