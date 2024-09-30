<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $isoDayOfWeek expression
 */
class IsoDayOfWeekOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                name: Expression::stringFieldPath('name'),
                dayOfWeek: Expression::isoDayOfWeek(
                    Expression::dateFieldPath('birthday'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::IsoDayOfWeekExample, $pipeline);
    }
}
