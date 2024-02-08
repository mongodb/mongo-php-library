<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $topN accumulator
 */
class TopNAccumulatorTest extends PipelineTestCase
{
    public function testComputingNBasedOnTheGroupKeyForGroup(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: object(
                    gameId: Expression::fieldPath('gameId'),
                ),
                gamescores: Accumulator::topN(
                    output: Expression::fieldPath('score'),
                    n: Expression::cond(
                        if: Expression::eq(
                            Expression::fieldPath('gameId'),
                            'G2',
                        ),
                        then: 1,
                        else: 3,
                    ),
                    sortBy: object(
                        score: Sort::Desc,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TopNComputingNBasedOnTheGroupKeyForGroup, $pipeline);
    }

    public function testFindTheThreeHighestScores(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                gameId: 'G1',
            ),
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                playerId: Accumulator::topN(
                    output: [
                        Expression::fieldPath('playerId'),
                        Expression::fieldPath('score'),
                    ],
                    sortBy: object(
                        score: Sort::Desc,
                    ),
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TopNFindTheThreeHighestScores, $pipeline);
    }

    public function testFindingTheThreeHighestScoreDocumentsAcrossMultipleGames(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                playerId: Accumulator::topN(
                    output: [
                        Expression::fieldPath('playerId'),
                        Expression::fieldPath('score'),
                    ],
                    sortBy: object(
                        score: Sort::Desc,
                    ),
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TopNFindingTheThreeHighestScoreDocumentsAcrossMultipleGames, $pipeline);
    }
}
