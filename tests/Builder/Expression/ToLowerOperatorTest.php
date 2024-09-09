<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $toLower expression
 */
class ToLowerOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: Expression::toLower(
                    Expression::stringFieldPath('item'),
                ),
                description: Expression::toLower(
                    Expression::stringFieldPath('description'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ToLowerExample, $pipeline);
    }
}
