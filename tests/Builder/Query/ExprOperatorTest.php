<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $expr query
 */
class ExprOperatorTest extends PipelineTestCase
{
    public function testCompareTwoFieldsFromASingleDocument(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::expr(
                    Expression::gt(
                        Expression::fieldPath('spent'),
                        Expression::fieldPath('budget'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ExprCompareTwoFieldsFromASingleDocument, $pipeline);
    }

    public function testUsingExprWithConditionalStatements(): void
    {
        $discountedPrice = Expression::cond(
            if: Expression::gte(Expression::fieldPath('qty'), 100),
            then: Expression::multiply(
                Expression::numberfieldPath('price'),
                0.5,
            ),
            else: Expression::multiply(
                Expression::numberfieldPath('price'),
                0.75,
            ),
        );

        $pipeline = new Pipeline(
            Stage::match(
                Query::expr(
                    Expression::lt($discountedPrice, 5),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ExprUsingExprWithConditionalStatements, $pipeline);
    }
}
