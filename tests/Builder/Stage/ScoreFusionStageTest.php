<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
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
                        'searchOne' => [
                            [
                                '$vectorSearch' => [
                                    'index' => 'vector_index',
                                    'path' => 'plot_embedding',
                                    'queryVector' => [-0.0016261312, -0.028070757, -0.011342932],
                                    'numCandidates' => 150,
                                    'limit' => 10,
                                ],
                            ],
                        ],
                        'searchTwo' => [
                            [
                                '$search' => [
                                    'index' => '<INDEX_NAME>',
                                    'text' => [
                                        'query' => '<QUERY_TERM>',
                                        'path' => '<FIELD_NAME>',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'normalization' => 'sigmoid',
                ],
                combination: [
                    'method' => 'expression',
                    'expression' => [
                        '$sum' => [
                            [
                                '$multiply' => [
                                    '$$searchOne',
                                    10,
                                ],
                            ],
                            '$$searchTwo',
                        ],
                    ],
                ],
                scoreDetails: true,
            ),
            Stage::project(
                _id: 1,
                title: 1,
                plot: 1,
                scoreDetails: ['$meta' => 'scoreDetails'],
            ),
            Stage::limit(20),
        );
        $this->assertSamePipeline(Pipelines::ScoreFusionExample, $pipeline);
    }
}
