<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $abs expression
 */
class AbsOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                delta: Expression::abs(
                    Expression::subtract(
                        Expression::numberFieldPath('startTemp'),
                        Expression::numberFieldPath('endTemp'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AbsExample, $pipeline);
    }
}
