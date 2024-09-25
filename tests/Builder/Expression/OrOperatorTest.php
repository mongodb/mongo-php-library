<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $or expression
 */
class OrOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                result: Expression::or(
                    Expression::gt(
                        Expression::numberFieldPath('qty'),
                        250,
                    ),
                    Expression::lt(
                        Expression::numberFieldPath('qty'),
                        200,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::OrExample, $pipeline);
    }
}
