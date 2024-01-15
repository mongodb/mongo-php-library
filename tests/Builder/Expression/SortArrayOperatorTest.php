<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $sortArray expression
 */
class SortArrayOperatorTest extends PipelineTestCase
{
    public function testSortAnArrayOfIntegers(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                result: Expression::sortArray(
                    input: [1, 4, 1, 6, 12, 5],
                    sortBy: 1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SortArraySortAnArrayOfIntegers, $pipeline);
    }

    public function testSortOnAField(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                result: Expression::sortArray(
                    input: Expression::arrayFieldPath('team'),
                    // @todo This object should be typed as "sort spec"
                    sortBy: object(
                        name: 1,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SortArraySortOnAField, $pipeline);
    }

    public function testSortOnASubfield(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                result: Expression::sortArray(
                    input: Expression::arrayFieldPath('team'),
                    // @todo This array should be typed as "sort spec"
                    sortBy: [
                        'address.city' => -1,
                    ],
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SortArraySortOnASubfield, $pipeline);
    }

    public function testSortOnMixedTypeFields(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                result: Expression::sortArray(
                    input: [
                        20,
                        4,
                        object(a: 'Free'),
                        6,
                        21,
                        5,
                        'Gratis',
                        ['a' => null],
                        object(a: object(sale: true, price: 19)),
                        10.23,
                        ['a' => 'On sale'],
                    ],
                    sortBy: 1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SortArraySortOnMixedTypeFields, $pipeline);
    }

    public function testSortOnMultipleFields(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                result: Expression::sortArray(
                    input: Expression::arrayFieldPath('team'),
                    // @todo This array should be typed as "sort spec"
                    sortBy: object(
                        age: -1,
                        name: 1,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SortArraySortOnMultipleFields, $pipeline);
    }
}
