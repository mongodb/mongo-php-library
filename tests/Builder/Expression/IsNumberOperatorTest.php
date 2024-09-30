<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $isNumber expression
 */
class IsNumberOperatorTest extends PipelineTestCase
{
    public function testConditionallyModifyFieldsUsingIsNumber(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                points: Expression::cond(
                    if: Expression::isNumber(
                        Expression::fieldPath('grade'),
                    ),
                    then: Expression::fieldPath('grade'),
                    else: Expression::switch(
                        branches: [
                            Expression::case(
                                case: Expression::eq(
                                    Expression::fieldPath('grade'),
                                    'A',
                                ),
                                then: 4,
                            ),
                            Expression::case(
                                case: Expression::eq(
                                    Expression::fieldPath('grade'),
                                    'B',
                                ),
                                then: 3,
                            ),
                            Expression::case(
                                case: Expression::eq(
                                    Expression::fieldPath('grade'),
                                    'C',
                                ),
                                then: 2,
                            ),
                            Expression::case(
                                case: Expression::eq(
                                    Expression::fieldPath('grade'),
                                    'D',
                                ),
                                then: 1,
                            ),
                            Expression::case(
                                case: Expression::eq(
                                    Expression::fieldPath('grade'),
                                    'F',
                                ),
                                then: 0,
                            ),
                        ],
                    ),
                ),
            ),
            Stage::group(
                _id: Expression::fieldPath('student_id'),
                GPA: Accumulator::avg(
                    Expression::fieldPath('points'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::IsNumberConditionallyModifyFieldsUsingIsNumber, $pipeline);
    }

    public function testUseIsNumberToCheckIfAFieldIsNumeric(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                isNumber: Expression::isNumber(
                    Expression::fieldPath('reading'),
                ),
                hasType: Expression::type(
                    Expression::fieldPath('reading'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::IsNumberUseIsNumberToCheckIfAFieldIsNumeric, $pipeline);
    }
}
