<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $mergeObjects expression
 */
class MergeObjectsOperatorTest extends PipelineTestCase
{
    public function testMergeObjects(): void
    {
        $pipeline = new Pipeline(
            Stage::lookup(
                as: 'fromItems',
                from: 'items',   // field in the orders collection
                localField: 'item', // field in the items collection
                foreignField: 'item',
            ),
            Stage::replaceRoot(
                Expression::mergeObjects(
                    Expression::arrayElemAt(Expression::arrayFieldPath('fromItems'), 0),
                    Expression::variable('ROOT'),
                ),
            ),
            Stage::project(
                fromItems: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::MergeObjectsMergeObjects, $pipeline);
    }
}
