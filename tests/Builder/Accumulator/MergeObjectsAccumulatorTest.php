<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $mergeObjects accumulator
 */
class MergeObjectsAccumulatorTest extends PipelineTestCase
{
    public function testMergeObjectsAsAnAccumulator(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::fieldPath('item'),
                mergedSales: Accumulator::mergeObjects(
                    Expression::fieldPath('quantity'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MergeObjectsMergeObjectsAsAnAccumulator, $pipeline);
    }
}
