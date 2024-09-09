<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $fill stage
 */
class FillStageTest extends PipelineTestCase
{
    public function testFillDataForDistinctPartitions(): void
    {
        $pipeline = new Pipeline(
            Stage::fill(
                sortBy: object(
                    date: Sort::Asc,
                ),
                partitionBy: object(
                    restaurant: Expression::stringFieldPath('restaurant'),
                ),
                output: object(
                    score: object(method: 'locf'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FillFillDataForDistinctPartitions, $pipeline);
    }

    public function testFillMissingFieldValuesBasedOnTheLastObservedValue(): void
    {
        $pipeline = new Pipeline(
            Stage::fill(
                sortBy: object(
                    date: Sort::Asc,
                ),
                output: object(
                    score: object(method: 'locf'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FillFillMissingFieldValuesBasedOnTheLastObservedValue, $pipeline);
    }

    public function testFillMissingFieldValuesWithAConstantValue(): void
    {
        $pipeline = new Pipeline(
            Stage::fill(
                output: object(
                    bootsSold: object(value: 0),
                    sandalsSold: object(value: 0),
                    sneakersSold: object(value: 0),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FillFillMissingFieldValuesWithAConstantValue, $pipeline);
    }

    public function testFillMissingFieldValuesWithLinearInterpolation(): void
    {
        $pipeline = new Pipeline(
            Stage::fill(
                sortBy: object(
                    time: Sort::Asc,
                ),
                output: object(
                    price: object(method: 'linear'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FillFillMissingFieldValuesWithLinearInterpolation, $pipeline);
    }

    public function testIndicateIfAFieldWasPopulatedUsingFill(): void
    {
        $pipeline = new Pipeline(
            Stage::set(
                valueExisted: Expression::ifNull(
                    Expression::toBool(
                        Expression::toString(
                            Expression::fieldPath('score'),
                        ),
                    ),
                    false,
                ),
            ),
            Stage::fill(
                sortBy: object(
                    date: Sort::Asc,
                ),
                output: object(
                    score: object(method: 'locf'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FillIndicateIfAFieldWasPopulatedUsingFill, $pipeline);
    }
}
