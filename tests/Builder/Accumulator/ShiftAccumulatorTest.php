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
 * Test $shift accumulator
 */
class ShiftAccumulatorTest extends PipelineTestCase
{
    public function testShiftUsingANegativeInteger(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('state'),
                sortBy: object(
                    quantity: Sort::Desc,
                ),
                output: object(
                    shiftQuantityForState: Accumulator::shift(
                        output: Expression::fieldPath('quantity'),
                        by: -1,
                        default: 'Not available',
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ShiftShiftUsingANegativeInteger, $pipeline);
    }

    public function testShiftUsingAPositiveInteger(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('state'),
                sortBy: object(
                    quantity: Sort::Desc,
                ),
                output: object(
                    shiftQuantityForState: Accumulator::shift(
                        output: Expression::fieldPath('quantity'),
                        by: 1,
                        default: 'Not available',
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ShiftShiftUsingAPositiveInteger, $pipeline);
    }
}
