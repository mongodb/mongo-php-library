<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $dateFromParts expression
 */
class DateFromPartsOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                date: Expression::dateFromParts(
                    year: 2017,
                    month: 2,
                    day: 8,
                    hour: 12,
                ),
                date_iso: Expression::dateFromParts(
                    isoWeekYear: 2017,
                    isoWeek: 6,
                    isoDayOfWeek: 3,
                    hour: 12,
                ),
                date_timezone: Expression::dateFromParts(
                    year: 2016,
                    month: 12,
                    day: 31,
                    hour: 23,
                    minute: 46,
                    second: 12,
                    timezone: 'America/New_York',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DateFromPartsExample, $pipeline);
    }
}
