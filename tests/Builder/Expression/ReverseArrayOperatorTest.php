<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $reverseArray expression
 */
class ReverseArrayOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                name: 1,
                reverseFavorites: Expression::reverseArray(
                    Expression::arrayFieldPath('favorites'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReverseArrayExample, $pipeline);
    }
}
