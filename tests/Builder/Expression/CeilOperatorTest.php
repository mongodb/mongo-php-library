<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $ceil expression
 */
class CeilOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                value: 1,
                ceilingValue: Expression::ceil(
                    Expression::numberFieldPath('value'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::CeilExample, $pipeline);
    }
}
