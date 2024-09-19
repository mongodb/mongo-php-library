<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $dateFromString expression
 */
class DateFromStringOperatorTest extends PipelineTestCase
{
    public function testConvertingDates(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                date: Expression::dateFromString(
                    dateString: Expression::stringFieldPath('date'),
                    timezone: 'America/New_York',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DateFromStringConvertingDates, $pipeline);
    }

    public function testOnError(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                date: Expression::dateFromString(
                    dateString: Expression::stringFieldPath('date'),
                    timezone: Expression::stringFieldPath('timezone'),
                    onError: Expression::stringFieldPath('date'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DateFromStringOnError, $pipeline);
    }

    public function testOnNull(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                date: Expression::dateFromString(
                    dateString: Expression::stringFieldPath('date'),
                    timezone: Expression::stringFieldPath('timezone'),
                    onNull: new UTCDateTime(0),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DateFromStringOnNull, $pipeline);
    }
}
