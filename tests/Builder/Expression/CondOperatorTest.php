<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $cond expression
 */
class CondOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                discount: Expression::cond(
                    if: Expression::gte(
                        Expression::intFieldPath('qty'),
                        250,
                    ),
                    then: 30,
                    else: 20,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::CondExample, $pipeline);
    }
}
