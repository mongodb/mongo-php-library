<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $ifNull expression
 */
class IfNullOperatorTest extends PipelineTestCase
{
    public function testMultipleInputExpressions(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                value: Expression::ifNull(
                    Expression::fieldPath('description'),
                    Expression::fieldPath('quantity'),
                    'Unspecified',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::IfNullMultipleInputExpressions, $pipeline);
    }

    public function testSingleInputExpression(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                description: Expression::ifNull(
                    Expression::fieldPath('description'),
                    'Unspecified',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::IfNullSingleInputExpression, $pipeline);
    }
}
