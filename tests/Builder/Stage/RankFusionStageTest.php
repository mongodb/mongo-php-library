<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $rankFusion stage
 */
class RankFusionStageTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::rankFusion(
                input: object(
                    pipelines: object(
                        searchPlot: new Pipeline(
                            Stage::search(
                                Search::text('plot', 'space'),
                                index: 'default',
                            ),
                        ),
                        searchGenre: new Pipeline(
                            Stage::search(
                                Search::text('genres', 'adventure'),
                                index: 'default',
                            ),
                        ),
                    ),
                ),
                combination: object(
                    weights: object(
                        searchPlot: 0.6,
                        searchGenre: 0.4,
                    ),
                ),
                scoreDetails: true,
            ),
            Stage::addFields(
                scoreDetails: ['$meta' => 'searchScoreDetails'],
            ),
        );

        $this->assertSamePipeline(Pipelines::RankFusionExample, $pipeline);
    }
}
