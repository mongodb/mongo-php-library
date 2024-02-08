<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $unwind stage
 */
class UnwindStageTest extends PipelineTestCase
{
    public function testGroupByUnwoundValues(): void
    {
        $pipeline = new Pipeline(
            Stage::unwind(
                path: Expression::arrayFieldPath('sizes'),
                preserveNullAndEmptyArrays: true,
            ),
            Stage::group(
                _id: Expression::fieldPath('sizes'),
                averagePrice: Accumulator::avg(
                    Expression::numberFieldPath('price'),
                ),
            ),
            Stage::sort(
                averagePrice: Sort::Desc,
            ),
        );

        $this->assertSamePipeline(Pipelines::UnwindGroupByUnwoundValues, $pipeline);
    }

    public function testIncludeArrayIndex(): void
    {
        $pipeline = new Pipeline(
            Stage::unwind(
                path: Expression::arrayFieldPath('sizes'),
                includeArrayIndex: 'arrayIndex',
            ),
        );

        $this->assertSamePipeline(Pipelines::UnwindIncludeArrayIndex, $pipeline);
    }

    public function testPreserveNullAndEmptyArrays(): void
    {
        $pipeline = new Pipeline(
            Stage::unwind(
                path: Expression::arrayFieldPath('sizes'),
                preserveNullAndEmptyArrays: true,
            ),
        );

        $this->assertSamePipeline(Pipelines::UnwindPreserveNullAndEmptyArrays, $pipeline);
    }

    public function testUnwindArray(): void
    {
        $pipeline = new Pipeline(
            Stage::unwind(Expression::arrayFieldPath('sizes')),
        );

        $this->assertSamePipeline(Pipelines::UnwindUnwindArray, $pipeline);
    }

    public function testUnwindEmbeddedArrays(): void
    {
        $pipeline = new Pipeline(
            Stage::unwind(Expression::arrayFieldPath('items')),
            Stage::unwind(Expression::arrayFieldPath('items.tags')),
            Stage::group(
                _id: Expression::fieldPath('items.tags'),
                totalSalesAmount: Accumulator::sum(
                    Expression::multiply(
                        Expression::numberFieldPath('items.price'),
                        Expression::numberFieldPath('items.quantity'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::UnwindUnwindEmbeddedArrays, $pipeline);
    }
}
