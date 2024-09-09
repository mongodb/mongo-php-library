<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $multiply expression
 */
class MultiplyOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                date: 1,
                item: 1,
                total: Expression::multiply(
                    Expression::intFieldPath('price'),
                    Expression::intFieldPath('quantity'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MultiplyExample, $pipeline);
    }
}
