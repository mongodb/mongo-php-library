<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $changeStream stage
 */
class ChangeStreamStageTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::changeStream(),
        );

        $this->assertSamePipeline(Pipelines::ChangeStreamExample, $pipeline);
    }
}
