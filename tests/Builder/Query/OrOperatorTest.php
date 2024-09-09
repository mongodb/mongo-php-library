<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $or query
 */
class OrOperatorTest extends PipelineTestCase
{
    public function testErrorHandling(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::or(
                    Query::query(
                        x: Query::eq(0),
                    ),
                    Query::expr(
                        Expression::eq(
                            Expression::divide(
                                1,
                                Expression::intFieldPath('x'),
                            ),
                            3,
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::OrErrorHandling, $pipeline);
    }

    public function testOrClauses(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::or(
                    Query::query(
                        quantity: Query::lt(20),
                    ),
                    Query::query(
                        price: 10,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::OrOrClauses, $pipeline);
    }
}
