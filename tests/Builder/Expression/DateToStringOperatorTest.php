<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $dateToString expression
 */
class DateToStringOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                yearMonthDayUTC: Expression::dateToString(
                    format: '%Y-%m-%d',
                    date: Expression::dateFieldPath('date'),
                ),
                timewithOffsetNY: Expression::dateToString(
                    format: '%H:%M:%S:%L%z',
                    date: Expression::dateFieldPath('date'),
                    timezone: 'America/New_York',
                ),
                timewithOffset430: Expression::dateToString(
                    format: '%H:%M:%S:%L%z',
                    date: Expression::dateFieldPath('date'),
                    timezone: '+04:30',
                ),
                minutesOffsetNY: Expression::dateToString(
                    format: '%Z',
                    date: Expression::dateFieldPath('date'),
                    timezone: 'America/New_York',
                ),
                minutesOffset430: Expression::dateToString(
                    format: '%Z',
                    date: Expression::dateFieldPath('date'),
                    timezone: '+04:30',
                ),
                abbreviated_month: Expression::dateToString(
                    format: '%b',
                    date: Expression::dateFieldPath('date'),
                    timezone: '+04:30',
                ),
                full_month: Expression::dateToString(
                    format: '%B',
                    date: Expression::dateFieldPath('date'),
                    timezone: '+04:30',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DateToStringExample, $pipeline);
    }
}
