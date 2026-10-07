<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $rerank stage
 */
class RerankStageTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::query(
                    plot: [Query::exists(true), Query::type('string')],
                ),
            ),
            Stage::sort(released: -1),
            Stage::rerank(
                model: 'rerank-2.5',
                query: object(text: 'a group of heroes band together to stop a powerful enemy and save the world'),
                path: ['title', 'plot'],
                numDocsToRerank: 100,
            ),
            Stage::addFields(
                rerankScore: Expression::meta('score'),
            ),
            Stage::limit(10),
            Stage::project(
                _id: 0,
                title: 1,
                plot: 1,
                rerankScore: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::RerankExample, $pipeline);
    }
}
