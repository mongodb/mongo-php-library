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
 * Test $dateAdd expression
 */
class DateAddOperatorTest extends PipelineTestCase
{
    public function testAddAFutureDate(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                expectedDeliveryDate: Expression::dateAdd(
                    startDate: Expression::dateFieldPath('purchaseDate'),
                    unit: TimeUnit::Day,
                    amount: 3,
                ),
            ),
            Stage::merge('shipping'),
        );

        $this->assertSamePipeline(Pipelines::DateAddAddAFutureDate, $pipeline);
    }

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
                    date: Expression::dateAdd(
                        startDate: Expression::dateFieldPath('login'),
                        unit: TimeUnit::Day,
                        amount: 1,
                        timezone: Expression::stringFieldPath('location'),
                    ),
                ),
                hours: Expression::dateToString(
                    format: '%Y-%m-%d %H:%M',
                    date: Expression::dateAdd(
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
                    date: Expression::dateAdd(
                        startDate: Expression::dateFieldPath('login'),
                        unit: TimeUnit::Day,
                        amount: 1,
                        timezone: Expression::stringFieldPath('location'),
                    ),
                    timezone: Expression::stringFieldPath('location'),
                ),
                hoursTZInfo: Expression::dateToString(
                    format: '%Y-%m-%d %H:%M',
                    date: Expression::dateAdd(
                        startDate: Expression::dateFieldPath('login'),
                        unit: TimeUnit::Hour,
                        amount: 24,
                        timezone: Expression::stringFieldPath('location'),
                    ),
                    timezone: Expression::stringFieldPath('location'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DateAddAdjustForDaylightSavingsTime, $pipeline);
    }

    public function testFilterOnADateRange(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::expr(
                    Expression::gt(
                        Expression::dateFieldPath('deliveryDate'),
                        Expression::dateAdd(
                            startDate: Expression::dateFieldPath('purchaseDate'),
                            unit: TimeUnit::Day,
                            amount: 5,
                        ),
                    ),
                ),
            ),
            Stage::project(
                _id: 0,
                custId: 1,
                purchased: Expression::dateToString(
                    format: '%Y-%m-%d',
                    date: Expression::dateFieldPath('purchaseDate'),
                ),
                delivery: Expression::dateToString(
                    format: '%Y-%m-%d',
                    date: Expression::dateFieldPath('deliveryDate'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DateAddFilterOnADateRange, $pipeline);
    }
}
