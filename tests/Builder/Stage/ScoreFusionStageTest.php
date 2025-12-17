<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $scoreFusion stage
 */
class ScoreFusionStageTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline();

        $this->assertSamePipeline(Pipelines::ScoreFusionExample, $pipeline);
    }
}
