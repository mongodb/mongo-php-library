<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $slice expression
 */
class SliceOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                name: 1,
                threeFavorites: Expression::slice(
                    Expression::arrayFieldPath('favorites'),
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SliceExample, $pipeline);
    }
}
