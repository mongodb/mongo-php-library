<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $getField expression
 */
class GetFieldOperatorTest extends PipelineTestCase
{
    public function testQueryAFieldInASubdocument(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::expr(
                    Expression::lte(
                        Expression::getField(
                            field: Expression::literal('$small'),
                            input: Expression::intFieldPath('quantity'),
                        ),
                        20,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::GetFieldQueryAFieldInASubdocument, $pipeline);
    }

    public function testQueryFieldsThatContainPeriods(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::expr(
                    Expression::gt(
                        Expression::getField('price.usd'),
                        200,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::GetFieldQueryFieldsThatContainPeriods, $pipeline);
    }

    public function testQueryFieldsThatStartWithADollarSign(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::expr(
                    Expression::gt(
                        Expression::getField(
                            Expression::literal('$price'),
                        ),
                        200,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::GetFieldQueryFieldsThatStartWithADollarSign, $pipeline);
    }
}
