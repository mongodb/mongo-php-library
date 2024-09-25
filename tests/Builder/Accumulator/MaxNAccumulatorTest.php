<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $maxN accumulator
 */
class MaxNAccumulatorTest extends PipelineTestCase
{
    public function testComputingNBasedOnTheGroupKeyForGroup(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: object(
                    gameId: Expression::fieldPath('gameId'),
                ),
                gamescores: Accumulator::maxN(
                    input: [
                        Expression::fieldPath('score'),
                        Expression::fieldPath('playerId'),
                    ],
                    n: Expression::cond(
                        if: Expression::eq(
                            Expression::fieldPath('gameId'),
                            'G2',
                        ),
                        then: 1,
                        else: 3,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MaxNComputingNBasedOnTheGroupKeyForGroup, $pipeline);
    }

    public function testFindTheMaximumThreeScoresForASingleGame(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                gameId: 'G1',
            ),
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                maxThreeScores: Accumulator::maxN(
                    input: [
                        Expression::fieldPath('score'),
                        Expression::fieldPath('playerId'),
                    ],
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MaxNFindTheMaximumThreeScoresForASingleGame, $pipeline);
    }

    public function testFindingTheMaximumThreeScoresAcrossMultipleGames(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                maxScores: Accumulator::maxN(
                    input: [
                        Expression::fieldPath('score'),
                        Expression::fieldPath('playerId'),
                    ],
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MaxNFindingTheMaximumThreeScoresAcrossMultipleGames, $pipeline);
    }
}
