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
 * Test $minN accumulator
 */
class MinNAccumulatorTest extends PipelineTestCase
{
    public function testComputingNBasedOnTheGroupKeyForGroup(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: object(
                    gameId: Expression::fieldPath('gameId'),
                ),
                gamescores: Accumulator::minN(
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

        $this->assertSamePipeline(Pipelines::MinNComputingNBasedOnTheGroupKeyForGroup, $pipeline);
    }

    public function testFindTheMinimumThreeScoresForASingleGame(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                gameId: 'G1',
            ),
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                minScores: Accumulator::minN(
                    input: [
                        Expression::fieldPath('score'),
                        Expression::fieldPath('playerId'),
                    ],
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MinNFindTheMinimumThreeScoresForASingleGame, $pipeline);
    }

    public function testFindingTheMinimumThreeDocumentsAcrossMultipleGames(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                minScores: Accumulator::minN(
                    input: [
                        Expression::fieldPath('score'),
                        Expression::fieldPath('playerId'),
                    ],
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MinNFindingTheMinimumThreeDocumentsAcrossMultipleGames, $pipeline);
    }
}
