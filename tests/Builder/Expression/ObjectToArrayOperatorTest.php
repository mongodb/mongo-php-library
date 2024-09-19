<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $objectToArray expression
 */
class ObjectToArrayOperatorTest extends PipelineTestCase
{
    public function testObjectToArrayExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                dimensions: Expression::objectToArray(
                    Expression::fieldPath('dimensions'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ObjectToArrayObjectToArrayExample, $pipeline);
    }

    public function testObjectToArrayToSumNestedFields(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                warehouses: Expression::objectToArray(
                    Expression::objectFieldPath('instock'),
                ),
            ),
            Stage::unwind(
                Expression::arrayFieldPath('warehouses'),
            ),
            Stage::group(
                _id: Expression::fieldPath('warehouses.k'),
                total: Accumulator::sum(
                    Expression::fieldPath('warehouses.v'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ObjectToArrayObjectToArrayToSumNestedFields, $pipeline);
    }
}
