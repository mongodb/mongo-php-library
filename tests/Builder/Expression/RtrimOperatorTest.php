<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $rtrim expression
 */
class RtrimOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                description: Expression::rtrim(
                    Expression::stringFieldPath('description'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RtrimExample, $pipeline);
    }
}
