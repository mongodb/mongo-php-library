<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $sortByCount stage
 */
class SortByCountStageTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::unwind(
                Expression::arrayFieldPath('tags'),
            ),
            Stage::sortByCount(
                Expression::arrayFieldPath('tags'),
            ),
        );

        $this->assertSamePipeline(Pipelines::SortByCountExample, $pipeline);
    }
}
