<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $sort stage
 */
class SortStageTest extends PipelineTestCase
{
    public function testAscendingDescendingSort(): void
    {
        $pipeline = new Pipeline(
            Stage::sort(
                object(
                    age: -1,
                    posts: 1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SortAscendingDescendingSort, $pipeline);
    }

    public function testTextScoreMetadataSort(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::text('operating'),
            ),
            Stage::sort(
                object(
                    score: ['$meta' => 'textScore'],
                    posts: -1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SortTextScoreMetadataSort, $pipeline);
    }
}
