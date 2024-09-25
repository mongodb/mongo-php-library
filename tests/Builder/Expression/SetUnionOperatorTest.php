<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $setUnion expression
 */
class SetUnionOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                flowerFieldA: 1,
                flowerFieldB: 1,
                allValues: Expression::setUnion(
                    Expression::arrayFieldPath('flowerFieldA'),
                    Expression::arrayFieldPath('flowerFieldB'),
                ),
                _id: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::SetUnionExample, $pipeline);
    }
}
