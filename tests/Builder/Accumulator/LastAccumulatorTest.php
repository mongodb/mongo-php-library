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
 * Test $last accumulator
 */
class LastAccumulatorTest extends PipelineTestCase
{
    public function testUseInGroupStage(): void
    {
        $pipeline = new Pipeline(
            Stage::sort(
                item: Sort::Asc,
                date: Sort::Asc,
            ),
            Stage::group(
                _id: '$item',
                lastSalesDate: Accumulator::last(Expression::dateFieldPath('date')),
            ),
        );

        $this->assertSamePipeline(Pipelines::LastUseInGroupStage, $pipeline);
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
                    lastOrderTypeForState: Accumulator::outputWindow(
                        Accumulator::last(Expression::stringFieldPath('type')),
                        documents: ['current', 'unbounded'],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::LastUseInSetWindowFieldsStage, $pipeline);
    }
}
