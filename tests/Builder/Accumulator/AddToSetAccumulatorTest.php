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
 * Test $addToSet accumulator
 */
class AddToSetAccumulatorTest extends PipelineTestCase
{
    public function testUseInGroupStage(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: object(
                    day: Expression::dayOfYear(Expression::dateFieldPath('date')),
                    year: Expression::year(Expression::dateFieldPath('date')),
                ),
                itemsSold: Accumulator::addToSet(Expression::arrayFieldPath('item')),
            ),
        );

        $this->assertSamePipeline(Pipelines::AddToSetUseInGroupStage, $pipeline);
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
                    cakeTypesForState: Accumulator::outputWindow(
                        Accumulator::addToSet(Expression::fieldPath('type')),
                        documents: [
                            'unbounded',
                            'current',
                        ],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AddToSetUseInSetWindowFieldsStage, $pipeline);
    }
}
