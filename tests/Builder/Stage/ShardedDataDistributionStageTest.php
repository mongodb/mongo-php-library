<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $shardedDataDistribution stage
 */
class ShardedDataDistributionStageTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::shardedDataDistribution(),
        );

        $this->assertSamePipeline(Pipelines::ShardedDataDistributionExample, $pipeline);
    }
}
