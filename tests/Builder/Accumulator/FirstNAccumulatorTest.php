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
 * Test $firstN accumulator
 */
class FirstNAccumulatorTest extends PipelineTestCase
{
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
}
