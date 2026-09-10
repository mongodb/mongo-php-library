<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Stage\FluentFactoryTrait;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $scoreFusion stage
 */
class ScoreFusionStageTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::scoreFusion(
                input: [
                    'pipelines' => [
                        'searchOne' => new Pipeline(
                            Stage::vectorSearch(
                                index: 'vector_index',
                                path: 'plot_embedding',
                                queryVector: [-0.0016261312, -0.028070757, -0.011342932],
                                numCandidates: 150,
                                limit: 10,
                            ),
                        ),
                        'searchTwo' => new Pipeline(
                            Stage::search(
                                Search::text(
                                    query: '<QUERY_TERM>',
                                    path: '<FIELD_NAME>',
                                ),
                                index: '<INDEX_NAME>',
                            ),
                        ),
                    ],
                    'normalization' => 'sigmoid',
                ],
                combination: [
                    'method' => 'expression',
                    'expression' => Expression::sum(
                        Expression::multiply(
                            Expression::variable('searchOne'),
                            10,
                        ),
                        Expression::variable('searchTwo'),
                    ),
                ],
                scoreDetails: true,
            ),
            Stage::project(
                _id: 1,
                title: 1,
                plot: 1,
                scoreDetails: Expression::meta('scoreDetails'),
            ),
            Stage::limit(20),
        );

        $this->assertSamePipeline(Pipelines::ScoreFusionExample, $pipeline);
    }

    public function testExampleFluent(): void
    {
        $factory = new class {
            use FluentFactoryTrait;
        };

        $pipeline = $factory
            ->scoreFusion(
                input: [
                    'pipelines' => [
                        'searchOne' => new Pipeline(
                            Stage::vectorSearch(
                                index: 'vector_index',
                                path: 'plot_embedding',
                                queryVector: [-0.0016261312, -0.028070757, -0.011342932],
                                numCandidates: 150,
                                limit: 10,
                            ),
                        ),
                        'searchTwo' => new Pipeline(
                            Stage::search(
                                Search::text(
                                    query: '<QUERY_TERM>',
                                    path: '<FIELD_NAME>',
                                ),
                                index: '<INDEX_NAME>',
                            ),
                        ),
                    ],
                    'normalization' => 'sigmoid',
                ],
                combination: [
                    'method' => 'expression',
                    'expression' => Expression::sum(
                        Expression::multiply(
                            Expression::variable('searchOne'),
                            10,
                        ),
                        Expression::variable('searchTwo'),
                    ),
                ],
                scoreDetails: true,
            )
            ->project(
                _id: 1,
                title: 1,
                plot: 1,
                scoreDetails: Expression::meta('scoreDetails'),
            )
            ->limit(20)
            ->getPipeline();

        $this->assertSamePipeline(Pipelines::ScoreFusionExample, $pipeline);
    }
}
