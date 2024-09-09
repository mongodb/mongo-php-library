<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $planCacheStats stage
 */
class PlanCacheStatsStageTest extends PipelineTestCase
{
    public function testFindCacheEntryDetailsForAQueryHash(): void
    {
        $pipeline = new Pipeline(
            Stage::planCacheStats(),
            Stage::match(
                planCacheKey: 'B1435201',
            ),
        );

        $this->assertSamePipeline(Pipelines::PlanCacheStatsFindCacheEntryDetailsForAQueryHash, $pipeline);
    }

    public function testReturnInformationForAllEntriesInTheQueryCache(): void
    {
        $pipeline = new Pipeline(
            Stage::planCacheStats(),
        );

        $this->assertSamePipeline(Pipelines::PlanCacheStatsReturnInformationForAllEntriesInTheQueryCache, $pipeline);
    }
}
