<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $second expression
 */
class SecondOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                seconds: Expression::second(
                    Expression::dateFieldPath('date'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SecondExample, $pipeline);
    }
}
