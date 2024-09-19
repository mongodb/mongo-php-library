<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $convert expression
 */
class ConvertOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                convertedPrice: Expression::convert(
                    input: Expression::fieldPath('price'),
                    to: 'decimal',
                    onError: 'Error',
                    onNull: new Decimal128('0'),
                ),
                convertedQty: Expression::convert(
                    input: Expression::fieldPath('qty'),
                    to: 'int',
                    onError: Expression::concat(
                        'Could not convert ',
                        Expression::toString(
                            Expression::fieldPath('qty'),
                        ),
                        ' to type integer.',
                    ),
                    onNull: 0,
                ),
            ),
            Stage::project(
                totalPrice: Expression::switch(
                    branches: [
                        Expression::case(
                            case: Expression::eq(
                                Expression::type(
                                    Expression::fieldPath('convertedPrice'),
                                ),
                                'string',
                            ),
                            then: 'NaN',
                        ),
                        Expression::case(
                            case: Expression::eq(
                                Expression::type(
                                    Expression::fieldPath('convertedQty'),
                                ),
                                'string',
                            ),
                            then: 'NaN',
                        ),
                    ],
                    default: Expression::multiply(
                        Expression::fieldPath('convertedPrice'),
                        Expression::fieldPath('convertedQty'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ConvertExample, $pipeline);
    }
}
