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
 * Test $rank accumulator
 */
class RankAccumulatorTest extends PipelineTestCase
{
    public function testRankPartitionsByADateField(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('state'),
                sortBy: object(
                    orderDate: 1,
                ),
                output: object(
                    rankOrderDateForState: Accumulator::rank(),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RankRankPartitionsByADateField, $pipeline);
    }

    public function testRankPartitionsByAnIntegerField(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::stringFieldPath('state'),
                sortBy: object(
                    quantity: -1,
                ),
                output: object(
                    rankQuantityForState: Accumulator::rank(),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RankRankPartitionsByAnIntegerField, $pipeline);
    }
}
