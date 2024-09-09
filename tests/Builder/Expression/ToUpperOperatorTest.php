<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $toUpper expression
 */
class ToUpperOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: Expression::toUpper(
                    Expression::stringFieldPath('item'),
                ),
                description: Expression::toUpper(
                    Expression::stringFieldPath('description'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ToUpperExample, $pipeline);
    }
}
