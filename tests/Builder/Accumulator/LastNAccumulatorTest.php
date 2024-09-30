<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $lastN accumulator
 */
class LastNAccumulatorTest extends PipelineTestCase
{
    public function testComputingNBasedOnTheGroupKeyForGroup(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: ['gameId' => Expression::arrayFieldPath('gameId')],
                gamescores: Accumulator::lastN(
                    input: Expression::arrayFieldPath('score'),
                    n: Expression::cond(
                        if: Expression::eq(
                            Expression::arrayFieldPath('gameId'),
                            'G2',
                        ),
                        then: 1,
                        else: 3,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::LastNComputingNBasedOnTheGroupKeyForGroup, $pipeline);
    }

    public function testFindTheLastThreePlayerScoresForASingleGame(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                gameId: 'G1',
            ),
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                lastThreeScores: Accumulator::lastN(
                    input: [
                        Expression::arrayFieldPath('playerId'),
                        Expression::arrayFieldPath('score'),
                    ],
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::LastNFindTheLastThreePlayerScoresForASingleGame, $pipeline);
    }

    public function testFindingTheLastThreePlayerScoresAcrossMultipleGames(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                playerId: Accumulator::lastN(
                    input: [
                        Expression::arrayFieldPath('playerId'),
                        Expression::arrayFieldPath('score'),
                    ],
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::LastNFindingTheLastThreePlayerScoresAcrossMultipleGames, $pipeline);
    }

    public function testUsingSortWithLastN(): void
    {
        $pipeline = new Pipeline(
            Stage::sort(
                score: Sort::Desc,
            ),
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                playerId: Accumulator::lastN(
                    input: [
                        Expression::arrayFieldPath('playerId'),
                        Expression::arrayFieldPath('score'),
                    ],
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::LastNUsingSortWithLastN, $pipeline);
    }
}
