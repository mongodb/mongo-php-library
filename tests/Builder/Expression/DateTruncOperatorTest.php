<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\TimeUnit;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $dateTrunc expression
 */
class DateTruncOperatorTest extends PipelineTestCase
{
    public function testTruncateOrderDatesAndObtainQuantitySumInAGroupPipelineStage(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: object(
                    truncatedOrderDate: Expression::dateTrunc(
                        date: Expression::dateFieldPath('orderDate'),
                        unit: TimeUnit::Month,
                        binSize: 6,
                    ),
                ),
                sumQuantity: Accumulator::sum(
                    Expression::intFieldPath('quantity'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DateTruncTruncateOrderDatesAndObtainQuantitySumInAGroupPipelineStage, $pipeline);
    }

    public function testTruncateOrderDatesInAProjectPipelineStage(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 1,
                orderDate: 1,
                truncatedOrderDate: Expression::dateTrunc(
                    date: Expression::dateFieldPath('orderDate'),
                    unit: TimeUnit::Week,
                    binSize: 2,
                    timezone: 'America/Los_Angeles',
                    startOfWeek: 'Monday',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DateTruncTruncateOrderDatesInAProjectPipelineStage, $pipeline);
    }
}
