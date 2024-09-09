<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $not expression
 */
class NotOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                result: Expression::not(
                    Expression::gt(
                        Expression::numberFieldPath('qty'),
                        250,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::NotExample, $pipeline);
    }
}
