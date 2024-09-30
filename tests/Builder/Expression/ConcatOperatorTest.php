<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $concat expression
 */
class ConcatOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                itemDescription: Expression::concat(
                    Expression::stringFieldPath('item'),
                    ' - ',
                    Expression::stringFieldPath('description'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ConcatExample, $pipeline);
    }
}
