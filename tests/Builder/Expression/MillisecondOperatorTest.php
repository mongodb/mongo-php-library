<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $millisecond expression
 */
class MillisecondOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                milliseconds: Expression::millisecond(
                    Expression::dateFieldPath('date'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MillisecondExample, $pipeline);
    }
}
