<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $elemMatch query
 */
class ElemMatchOperatorTest extends PipelineTestCase
{
    public function testArrayOfEmbeddedDocuments(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                results: Query::elemMatch(
                    Query::query(
                        product: 'xyz',
                        score: Query::gte(8),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ElemMatchArrayOfEmbeddedDocuments, $pipeline);
    }

    public function testElementMatch(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                results: Query::elemMatch(
                    Query::fieldQuery(
                        Query::gte(80),
                        Query::lt(85),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ElemMatchElementMatch, $pipeline);
    }

    public function testSingleFieldOperator(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                results: Query::elemMatch(
                    Query::gt(10),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ElemMatchSingleFieldOperator, $pipeline);
    }

    public function testSingleQueryCondition(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                results: Query::elemMatch(
                    Query::query(
                        product: Query::ne('xyz'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ElemMatchSingleQueryCondition, $pipeline);
    }

    public function testUsingOrWithElemMatch(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                game: Query::elemMatch(
                    Query::or(
                        Query::query(score: Query::gt(10)),
                        Query::query(score: Query::lt(5)),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ElemMatchUsingOrWithElemMatch, $pipeline);
    }
}
