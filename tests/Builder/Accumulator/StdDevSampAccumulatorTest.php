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
 * Test $stdDevSamp accumulator
 */
class StdDevSampAccumulatorTest extends PipelineTestCase
{
    public function testUseInGroupStage(): void
    {
        $pipeline = new Pipeline(
            Stage::sample(100),
            Stage::group(
                _id: null,
                ageStdDev: Accumulator::stdDevSamp(
                    Expression::numberFieldPath('age'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::StdDevSampUseInGroupStage, $pipeline);
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
                    stdDevSampQuantityForState: Accumulator::outputWindow(
                        Accumulator::stdDevSamp(
                            Expression::numberFieldPath('quantity'),
                        ),
                        documents: ['unbounded', 'current'],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::StdDevSampUseInSetWindowFieldsStage, $pipeline);
    }
}
