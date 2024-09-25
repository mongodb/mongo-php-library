<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $reduce expression
 */
class ReduceOperatorTest extends PipelineTestCase
{
    public function testArrayConcatenation(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                collapsed: Expression::reduce(
                    input: Expression::arrayFieldPath('arr'),
                    initialValue: [],
                    in: Expression::concatArrays(
                        Expression::variable('value'),
                        Expression::variable('this'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReduceArrayConcatenation, $pipeline);
    }

    public function testComputingAMultipleReductions(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                results: Expression::reduce(
                    Expression::arrayFieldPath('arr'),
                    [],
                    object(
                        collapsed: Expression::concatArrays(
                            Expression::variable('value.collapsed'),
                            Expression::variable('this'),
                        ),
                        firstValues: Expression::concatArrays(
                            Expression::variable('value.firstValues'),
                            Expression::slice(
                                Expression::variable('this'),
                                1,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReduceComputingAMultipleReductions, $pipeline);
    }

    public function testDiscountedMerchandise(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                discountedPrice: Expression::reduce(
                    input: Expression::arrayFieldPath('discounts'),
                    initialValue: Expression::numberFieldPath('price'),
                    in: Expression::multiply(
                        Expression::variable('value'),
                        Expression::subtract(
                            1,
                            Expression::variable('this'),
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReduceDiscountedMerchandise, $pipeline);
    }

    public function testMultiplication(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::objectIdFieldPath('experimentId'),
                probabilityArr: Accumulator::push(
                    Expression::fieldPath('probability'),
                ),
            ),
            Stage::project(
                description: 1,
                results: Expression::reduce(
                    input: Expression::arrayFieldPath('probabilityArr'),
                    initialValue: 1,
                    in: Expression::multiply(
                        Expression::variable('value'),
                        Expression::variable('this'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReduceMultiplication, $pipeline);
    }

    public function testStringConcatenation(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                hobbies: Query::gt([]),
            ),
            Stage::project(
                name: 1,
                bio: Expression::reduce(
                    input: Expression::arrayFieldPath('hobbies'),
                    initialValue: 'My hobbies include:',
                    in: Expression::concat(
                        Expression::variable('value'),
                        Expression::cond(
                            if: Expression::eq(
                                Expression::variable('value'),
                                'My hobbies include:',
                            ),
                            then: ' ',
                            else: ', ',
                        ),
                        Expression::variable('this'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReduceStringConcatenation, $pipeline);
    }
}
