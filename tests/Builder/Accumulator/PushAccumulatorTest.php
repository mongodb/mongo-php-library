<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $push accumulator
 */
class PushAccumulatorTest extends PipelineTestCase
{
    public function testUseInGroupStage(): void
    {
        $pipeline = new Pipeline(
            Stage::sort(
                object(
                    date: 1,
                    item: 1,
                ),
            ),
            Stage::group(
                _id: object(
                    day: Expression::dayOfYear(
                        Expression::dateFieldPath('date'),
                    ),
                    year: Expression::year(
                        Expression::dateFieldPath('date'),
                    ),
                ),
                itemsSold: Accumulator::push(
                    object(
                        item: Expression::fieldPath('item'),
                        quantity: Expression::intFieldPath('quantity'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::PushUseInGroupStage, $pipeline);
    }

    public function testUseInSetWindowFieldsStage(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::fieldPath('state'),
                sortBy: object(
                    orderDate: 1,
                ),
                output: object(
                    quantitiesForState: Accumulator::outputWindow(
                        Accumulator::push(
                            Expression::numberFieldPath('quantity'),
                        ),
                        documents: ['unbounded', 'current'],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::PushUseInSetWindowFieldsStage, $pipeline);
    }
}
