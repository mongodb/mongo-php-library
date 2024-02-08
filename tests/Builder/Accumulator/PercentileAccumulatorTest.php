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
 * Test $percentile accumulator
 */
class PercentileAccumulatorTest extends PipelineTestCase
{
    public function testCalculateASingleValueAsAnAccumulator(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: null,
                test01_percentiles: Accumulator::percentile(
                    input: Expression::numberFieldPath('test01'),
                    p: [0.95],
                    method: 'approximate',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::PercentileCalculateASingleValueAsAnAccumulator, $pipeline);
    }

    public function testCalculateMultipleValuesAsAnAccumulator(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: null,
                test01_percentiles: Accumulator::percentile(
                    input: Expression::numberFieldPath('test01'),
                    p: [0.5, 0.75, 0.9, 0.95],
                    method: 'approximate',
                ),
                test02_percentiles: Accumulator::percentile(
                    input: Expression::numberFieldPath('test02'),
                    p: [0.5, 0.75, 0.9, 0.95],
                    method: 'approximate',
                ),
                test03_percentiles: Accumulator::percentile(
                    input: Expression::numberFieldPath('test03'),
                    p: [0.5, 0.75, 0.9, 0.95],
                    method: 'approximate',
                ),
                test03_percent_alt: Accumulator::percentile(
                    input: Expression::numberFieldPath('test03'),
                    p: [0.9, 0.5, 0.75, 0.95],
                    method: 'approximate',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::PercentileCalculateMultipleValuesAsAnAccumulator, $pipeline);
    }

    public function testUsePercentileInASetWindowFieldStage(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                sortBy: object(
                    test01: Sort::Asc,
                ),
                output: object(
                    test01_95percentile: Accumulator::outputWindow(
                        Accumulator::percentile(
                            input: Expression::numberFieldPath('test01'),
                            p: [0.95],
                            method: 'approximate',
                        ),
                        range: [-3, 3],
                    ),
                ),
            ),
            Stage::project(
                _id: 0,
                studentId: 1,
                test01_95percentile: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::PercentileUsePercentileInASetWindowFieldStage, $pipeline);
    }
}
