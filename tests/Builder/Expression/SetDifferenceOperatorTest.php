<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $setDifference expression
 */
class SetDifferenceOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                flowerFieldA: 1,
                flowerFieldB: 1,
                inBOnly: Expression::setDifference(
                    Expression::arrayFieldPath('flowerFieldB'),
                    Expression::arrayFieldPath('flowerFieldA'),
                ),
                _id: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::SetDifferenceExample, $pipeline);
    }
}
