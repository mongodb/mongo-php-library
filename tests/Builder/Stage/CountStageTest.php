<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $count stage
 */
class CountStageTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                score: Query::gt(80),
            ),
            Stage::count('passing_scores'),
        );

        $this->assertSamePipeline(Pipelines::CountExample, $pipeline);
    }
}
