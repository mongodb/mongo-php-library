<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $hour expression
 */
class HourOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                hour: Expression::hour(
                    Expression::dateFieldPath('date'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::HourExample, $pipeline);
    }
}
