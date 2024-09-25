<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $setIsSubset expression
 */
class SetIsSubsetOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                flowerFieldA: 1,
                flowerFieldB: 1,
                AisSubset: Expression::setIsSubset(
                    Expression::arrayFieldPath('flowerFieldA'),
                    Expression::arrayFieldPath('flowerFieldB'),
                ),
                _id: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::SetIsSubsetExample, $pipeline);
    }
}
