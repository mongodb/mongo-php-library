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
 * Test $median accumulator
 */
class MedianAccumulatorTest extends PipelineTestCase
{
    public function testUseMedianAsAnAccumulator(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: null,
                test01_median: Accumulator::median(
                    input: Expression::intFieldPath('test01'),
                    method: 'approximate',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MedianUseMedianAsAnAccumulator, $pipeline);
    }

    public function testUseMedianInASetWindowFieldStage(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                sortBy: object(
                    test01: Sort::Asc,
                ),
                output: object(
                    test01_median: Accumulator::outputWindow(
                        Accumulator::median(
                            input: Expression::intFieldPath('test01'),
                            method: 'approximate',
                        ),
                        range: [-3, 3],
                    ),
                ),
            ),
            Stage::project(
                _id: 0,
                studentId: 1,
                test01_median: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::MedianUseMedianInASetWindowFieldStage, $pipeline);
    }
}
