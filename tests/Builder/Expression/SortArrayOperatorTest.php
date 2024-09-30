<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
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
                    sortBy: Sort::Asc,
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
                        name: Sort::Asc,
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
                    sortBy: [
                        'address.city' => Sort::Desc,
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
                        new Decimal128('10.23'),
                        ['a' => 'On sale'],
                    ],
                    sortBy: Sort::Asc,
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
                        age: Sort::Desc,
                        name: Sort::Asc,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SortArraySortOnMultipleFields, $pipeline);
    }
}
