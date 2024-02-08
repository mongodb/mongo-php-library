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
 * Test $firstN accumulator
 */
class FirstNAccumulatorTest extends PipelineTestCase
{
    public function testComputingNBasedOnTheGroupKeyForGroup(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: object(
                    gameId: Expression::fieldPath('gameId'),
                ),
                gamescores: Accumulator::firstN(
                    input: Expression::fieldPath('score'),
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

        $this->assertSamePipeline(Pipelines::FirstNComputingNBasedOnTheGroupKeyForGroup, $pipeline);
    }

    public function testFindTheFirstThreePlayerScoresForASingleGame(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                gameId: 'G1',
            ),
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                firstThreeScores: Accumulator::firstN(
                    input: [
                        Expression::fieldPath('playerId'),
                        Expression::fieldPath('score'),
                    ],
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FirstNFindTheFirstThreePlayerScoresForASingleGame, $pipeline);
    }

    public function testFindingTheFirstThreePlayerScoresAcrossMultipleGames(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                playerId: Accumulator::firstN(
                    input: [
                        Expression::fieldPath('playerId'),
                        Expression::fieldPath('score'),
                    ],
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FirstNFindingTheFirstThreePlayerScoresAcrossMultipleGames, $pipeline);
    }

    public function testNullAndMissingValues(): void
    {
        $pipeline = new Pipeline(
            Stage::documents([
                object(playerId: 'PlayerA', gameId: 'G1', score: 1),
                object(playerId: 'PlayerB', gameId: 'G1', score: 2),
                object(playerId: 'PlayerC', gameId: 'G1', score: 3),
                object(playerId: 'PlayerD', gameId: 'G1'),
                object(playerId: 'PlayerE', gameId: 'G1', score: null),
            ]),
            Stage::group(
                _id: Expression::stringFieldPath('gameId'),
                firstFiveScores: Accumulator::firstN(
                    input: Expression::fieldPath('score'),
                    n: 5,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FirstNNullAndMissingValues, $pipeline);
    }

    public function testUsingSortWithFirstN(): void
    {
        $pipeline = new Pipeline(
            Stage::sort(
                score: Sort::Desc,
            ),
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                playerId: Accumulator::firstN(
                    input: [
                        Expression::fieldPath('playerId'),
                        Expression::fieldPath('score'),
                    ],
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FirstNUsingSortWithFirstN, $pipeline);
    }
}
