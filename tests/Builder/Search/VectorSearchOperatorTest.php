<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test vectorSearch search
 */
class VectorSearchOperatorTest extends PipelineTestCase
{
    public function testANNBasic(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::vectorSearch(
                    path: 'plot_embedding',
                    queryVector: [-0.0016261312, -0.028070757, -0.011342932],
                    limit: 10,
                    numCandidates: 150,
                ),
            ),
            Stage::project(
                _id: 0,
                plot: 1,
                title: 1,
                score: Expression::meta('searchScore'),
            ),
        );

        $this->assertSamePipeline(Pipelines::VectorSearchANNBasic, $pipeline);
    }

    public function testANNFilter(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::vectorSearch(
                    path: 'plot_embedding',
                    queryVector: [0.02421053, -0.022372592, -0.006231137],
                    limit: 10,
                    numCandidates: 150,
                    filter: Search::range(
                        path: 'year',
                        lt: 1975,
                    ),
                ),
            ),
            Stage::project(
                _id: 0,
                title: 1,
                plot: 1,
                year: 1,
                score: Expression::meta('searchScore'),
            ),
        );

        $this->assertSamePipeline(Pipelines::VectorSearchANNFilter, $pipeline);
    }

    public function testENN(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::vectorSearch(
                    path: 'plot_embedding',
                    queryVector: [-0.006954097, -0.009932499, -0.001311474],
                    limit: 10,
                    exact: true,
                ),
            ),
            Stage::project(
                _id: 0,
                plot: 1,
                title: 1,
                score: Expression::meta('searchScore'),
            ),
        );

        $this->assertSamePipeline(Pipelines::VectorSearchENN, $pipeline);
    }
}
