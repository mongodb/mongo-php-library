<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $subtract expression
 */
class SubtractOperatorTest extends PipelineTestCase
{
    public function testSubtractMillisecondsFromADate(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                dateDifference: Expression::subtract(
                    Expression::dateFieldPath('date'),
                    5 * 60 * 1000,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SubtractSubtractMillisecondsFromADate, $pipeline);
    }

    public function testSubtractNumbers(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                total: Expression::subtract(
                    Expression::add(
                        Expression::numberFieldPath('price'),
                        Expression::numberFieldPath('fee'),
                    ),
                    Expression::numberFieldPath('discount'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SubtractSubtractNumbers, $pipeline);
    }

    public function testSubtractTwoDates(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                dateDifference: Expression::subtract(
                    Expression::variable('NOW'),
                    Expression::dateFieldPath('date'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SubtractSubtractTwoDates, $pipeline);
    }
}
