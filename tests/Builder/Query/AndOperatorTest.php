<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $and query
 */
class AndOperatorTest extends PipelineTestCase
{
    public function testANDQueriesWithMultipleExpressionsSpecifyingTheSameField(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::and(
                    Query::query(
                        price: Query::ne(1.99),
                    ),
                    Query::query(
                        price: Query::exists(),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AndANDQueriesWithMultipleExpressionsSpecifyingTheSameField, $pipeline);
    }

    public function testANDQueriesWithMultipleExpressionsSpecifyingTheSameOperator(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::and(
                    Query::or(
                        Query::query(
                            qty: Query::lt(10),
                        ),
                        Query::query(
                            qty: Query::gt(50),
                        ),
                    ),
                    Query::or(
                        Query::query(
                            sale: true,
                        ),
                        Query::query(
                            price: Query::lt(5),
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AndANDQueriesWithMultipleExpressionsSpecifyingTheSameOperator, $pipeline);
    }
}
