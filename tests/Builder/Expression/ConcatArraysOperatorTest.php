<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $concatArrays expression
 */
class ConcatArraysOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                items: Expression::concatArrays(
                    Expression::arrayFieldPath('instock'),
                    Expression::arrayFieldPath('ordered'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ConcatArraysExample, $pipeline);
    }
}
