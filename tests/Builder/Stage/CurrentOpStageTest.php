<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $currentOp stage
 */
class CurrentOpStageTest extends PipelineTestCase
{
    public function testInactiveSessions(): void
    {
        $pipeline = new Pipeline(
            Stage::currentOp(
                allUsers: true,
                idleSessions: true,
            ),
            Stage::match(
                active: false,
                transaction: Query::exists(),
            ),
        );

        $this->assertSamePipeline(Pipelines::CurrentOpInactiveSessions, $pipeline);
    }

    public function testSampledQueries(): void
    {
        $pipeline = new Pipeline(
            Stage::currentOp(
                allUsers: true,
                localOps: true,
            ),
            Stage::match(
                desc: 'query analyzer',
            ),
        );

        $this->assertSamePipeline(Pipelines::CurrentOpSampledQueries, $pipeline);
    }
}
