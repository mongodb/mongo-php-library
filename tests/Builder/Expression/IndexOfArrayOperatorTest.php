<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $indexOfArray expression
 */
class IndexOfArrayOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                index: Expression::indexOfArray(
                    array: Expression::arrayFieldPath('items'),
                    search: 2,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::IndexOfArrayExample, $pipeline);
    }
}
