<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $dateToParts expression
 */
class DateToPartsOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                date: Expression::dateToParts(
                    Expression::dateFieldPath('date'),
                ),
                date_iso: Expression::dateToParts(
                    date: Expression::dateFieldPath('date'),
                    iso8601: true,
                ),
                date_timezone: Expression::dateToParts(
                    date: Expression::dateFieldPath('date'),
                    timezone: 'America/New_York',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DateToPartsExample, $pipeline);
    }
}
