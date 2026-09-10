<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $score stage
 */
class ScoreStageTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::score(
                score: Expression::meta('vectorSearchScore'),
            ),
        );

        $this->assertSamePipeline(Pipelines::ScoreExample, $pipeline);
    }
}
