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
 * Test $bottom accumulator
 */
class BottomAccumulatorTest extends PipelineTestCase
{
    public function testFindTheBottomScore(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                gameId: 'G1',
            ),
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                playerId: Accumulator::bottom(
                    output: [
                        Expression::fieldPath('playerId'),
                        Expression::fieldPath('score'),
                    ],
                    sortBy: object(
                        score: Sort::Desc,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BottomFindTheBottomScore, $pipeline);
    }

    public function testFindingTheBottomScoreAcrossMultipleGames(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::fieldPath('gameId'),
                playerId: Accumulator::bottom(
                    output: [
                        Expression::fieldPath('playerId'),
                        Expression::fieldPath('score'),
                    ],
                    sortBy: object(
                        score: Sort::Desc,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BottomFindingTheBottomScoreAcrossMultipleGames, $pipeline);
    }
}
