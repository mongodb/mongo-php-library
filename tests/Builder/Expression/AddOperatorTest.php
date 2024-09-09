<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $add expression
 */
class AddOperatorTest extends PipelineTestCase
{
    public function testAddNumbers(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                total: Expression::add(
                    Expression::fieldPath('price'),
                    Expression::fieldPath('fee'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AddAddNumbers, $pipeline);
    }

    public function testPerformAdditionOnADate(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                billing_date: Expression::add(
                    Expression::fieldPath('date'),
                    3 * 24 * 60 * 60000,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AddPerformAdditionOnADate, $pipeline);
    }
}
