<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $match stage
 */
class MatchStageTest extends PipelineTestCase
{
    public function testEqualityMatch(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                author: 'dave',
            ),
        );

        $this->assertSamePipeline(Pipelines::MatchEqualityMatch, $pipeline);
    }

    public function testPerformACount(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::or(
                    Query::query(
                        score: [
                            Query::gt(70),
                            Query::lt(90),
                        ],
                    ),
                    Query::query(
                        views: Query::gte(1000),
                    ),
                ),
            ),
            Stage::group(
                _id: null,
                count: Accumulator::sum(1),
            ),
        );

        $this->assertSamePipeline(Pipelines::MatchPerformACount, $pipeline);
    }
}
