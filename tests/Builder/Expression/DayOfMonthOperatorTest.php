<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $dayOfMonth expression
 */
class DayOfMonthOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                day: Expression::dayOfMonth(
                    Expression::dateFieldPath('date'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DayOfMonthExample, $pipeline);
    }
}
