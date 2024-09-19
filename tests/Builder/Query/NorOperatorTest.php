<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $nor query
 */
class NorOperatorTest extends PipelineTestCase
{
    public function testAdditionalComparisons(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::nor(
                    Query::query(
                        price: 1.99,
                    ),
                    Query::query(
                        qty: Query::lt(20),
                    ),
                    Query::query(
                        sale: true,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::NorAdditionalComparisons, $pipeline);
    }

    public function testNorAndExists(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::nor(
                    Query::query(
                        price: 1.99,
                    ),
                    Query::query(
                        price: Query::exists(false),
                    ),
                    Query::query(
                        sale: true,
                    ),
                    Query::query(
                        sale: Query::exists(false),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::NorNorAndExists, $pipeline);
    }

    public function testQueryWithTwoExpressions(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::nor(
                    Query::query(
                        price: 1.99,
                    ),
                    Query::query(
                        sale: true,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::NorQueryWithTwoExpressions, $pipeline);
    }
}
