<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
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
                date: Sort::Asc,
                item: Sort::Asc,
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
                    orderDate: Sort::Asc,
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
