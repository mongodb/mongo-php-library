<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\TimeUnit;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $dateDiff expression
 */
class DateDiffOperatorTest extends PipelineTestCase
{
    public function testElapsedTime(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: null,
                averageTime: Accumulator::avg(
                    Expression::dateDiff(
                        startDate: Expression::dateFieldPath('purchased'),
                        endDate: Expression::dateFieldPath('delivered'),
                        unit: TimeUnit::Day,
                    ),
                ),
            ),
            Stage::project(
                _id: 0,
                numDays: Expression::trunc(
                    Expression::numberFieldPath('averageTime'),
                    1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DateDiffElapsedTime, $pipeline);
    }

    public function testResultPrecision(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                Start: Expression::dateFieldPath('start'),
                End: Expression::dateFieldPath('end'),
                years: Expression::dateDiff(
                    startDate: Expression::dateFieldPath('start'),
                    endDate: Expression::dateFieldPath('end'),
                    unit: TimeUnit::Year,
                ),
                months: Expression::dateDiff(
                    startDate: Expression::dateFieldPath('start'),
                    endDate: Expression::dateFieldPath('end'),
                    unit: TimeUnit::Month,
                ),
                days: Expression::dateDiff(
                    startDate: Expression::dateFieldPath('start'),
                    endDate: Expression::dateFieldPath('end'),
                    unit: TimeUnit::Day,
                ),
                _id: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::DateDiffResultPrecision, $pipeline);
    }

    public function testWeeksPerMonth(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                wks_default: Expression::dateDiff(
                    startDate: Expression::dateFieldPath('start'),
                    endDate: Expression::dateFieldPath('end'),
                    unit: TimeUnit::Week,
                ),
                wks_monday: Expression::dateDiff(
                    startDate: Expression::dateFieldPath('start'),
                    endDate: Expression::dateFieldPath('end'),
                    unit: TimeUnit::Week,
                    startOfWeek: 'Monday',
                ),
                wks_friday: Expression::dateDiff(
                    startDate: Expression::dateFieldPath('start'),
                    endDate: Expression::dateFieldPath('end'),
                    unit: TimeUnit::Week,
                    startOfWeek: 'fri',
                ),
                _id: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::DateDiffWeeksPerMonth, $pipeline);
    }
}
