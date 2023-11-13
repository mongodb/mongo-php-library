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
 * Test $first accumulator
 */
class FirstAccumulatorTest extends PipelineTestCase
{
    public function testUseInGroupStage(): void
    {
        $pipeline = new Pipeline(
            Stage::sort(object(
                item: 1,
                date: 1,
            )),
            Stage::group(
                _id: Expression::fieldPath('item'),
                firstSale: Accumulator::first(Expression::dateFieldPath('date')),
            ),
        );

        $this->assertSamePipeline(Pipelines::FirstUseInGroupStage, $pipeline);
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
                    firstOrderTypeForState: Accumulator::outputWindow(
                        Accumulator::first(Expression::stringFieldPath('type')),
                        documents: ['unbounded', 'current'],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FirstUseInSetWindowFieldsStage, $pipeline);
    }
}
