<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\TimeUnit;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $dateSubtract expression
 */
class DateSubtractOperatorTest extends PipelineTestCase
{
    public function testAdjustForDaylightSavingsTime(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                location: 1,
                start: Expression::dateToString(
                    format: '%Y-%m-%d %H:%M',
                    date: Expression::dateFieldPath('login'),
                ),
                days: Expression::dateToString(
                    format: '%Y-%m-%d %H:%M',
                    date: Expression::dateSubtract(
                        startDate: Expression::dateFieldPath('login'),
                        unit: TimeUnit::Day,
                        amount: 1,
                        timezone: Expression::stringFieldPath('location'),
                    ),
                ),
                hours: Expression::dateToString(
                    format: '%Y-%m-%d %H:%M',
                    date: Expression::dateSubtract(
                        startDate: Expression::dateFieldPath('login'),
                        unit: TimeUnit::Hour,
                        amount: 24,
                        timezone: Expression::stringFieldPath('location'),
                    ),
                ),
                startTZInfo: Expression::dateToString(
                    format: '%Y-%m-%d %H:%M',
                    date: Expression::dateFieldPath('login'),
                    timezone: Expression::stringFieldPath('location'),
                ),
                daysTZInfo: Expression::dateToString(
                    format: '%Y-%m-%d %H:%M',
                    date: Expression::dateSubtract(
                        startDate: Expression::dateFieldPath('login'),
                        unit: TimeUnit::Day,
                        amount: 1,
                        timezone: Expression::stringFieldPath('location'),
                    ),
                    timezone: Expression::stringFieldPath('location'),
                ),
                hoursTZInfo: Expression::dateToString(
                    format: '%Y-%m-%d %H:%M',
                    date: Expression::dateSubtract(
                        startDate: Expression::dateFieldPath('login'),
                        unit: TimeUnit::Hour,
                        amount: 24,
                        timezone: Expression::stringFieldPath('location'),
                    ),
                    timezone: Expression::stringFieldPath('location'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DateSubtractAdjustForDaylightSavingsTime, $pipeline);
    }

    public function testFilterByRelativeDates(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::expr(
                    Expression::gt(
                        Expression::dateFieldPath('logoutTime'),
                        Expression::dateSubtract(
                            startDate: Expression::variable('NOW'),
                            unit: TimeUnit::Week,
                            amount: 1,
                        ),
                    ),
                ),
            ),
            Stage::project(
                _id: 0,
                custId: 1,
                loggedOut: Expression::dateToString(
                    format: '%Y-%m-%d',
                    date: Expression::dateFieldPath('logoutTime'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DateSubtractFilterByRelativeDates, $pipeline);
    }

    public function testSubtractAFixedAmount(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::expr(
                    Expression::eq(
                        Expression::month(
                            Expression::dateFieldPath('logout'),
                        ),
                        1,
                    ),
                ),
            ),
            Stage::project(
                logoutTime: Expression::dateSubtract(
                    startDate: Expression::dateFieldPath('logout'),
                    unit: TimeUnit::Hour,
                    amount: 3,
                ),
            ),
            Stage::merge('connectionTime'),
        );

        $this->assertSamePipeline(Pipelines::DateSubtractSubtractAFixedAmount, $pipeline);
    }
}
