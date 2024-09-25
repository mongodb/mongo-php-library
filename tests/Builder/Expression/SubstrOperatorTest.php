<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $substr expression
 */
class SubstrOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                yearSubstring: Expression::substr(
                    Expression::stringFieldPath('quarter'),
                    0,
                    2,
                ),
                quarterSubtring: Expression::substr(
                    Expression::stringFieldPath('quarter'),
                    2,
                    -1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SubstrExample, $pipeline);
    }
}
