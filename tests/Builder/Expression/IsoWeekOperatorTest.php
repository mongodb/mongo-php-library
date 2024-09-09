<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $isoWeek expression
 */
class IsoWeekOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                city: Expression::stringFieldPath('city'),
                weekNumber: Expression::isoWeek(
                    Expression::dateFieldPath('date'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::IsoWeekExample, $pipeline);
    }
}
