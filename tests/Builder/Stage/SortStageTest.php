<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $sort stage
 */
class SortStageTest extends PipelineTestCase
{
    public function testAscendingDescendingSort(): void
    {
        $pipeline = new Pipeline(
            Stage::sort(
                age: Sort::Desc,
                posts: Sort::Asc,
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
                score: Sort::TextScore,
                posts: Sort::Desc,
            ),
        );

        $this->assertSamePipeline(Pipelines::SortTextScoreMetadataSort, $pipeline);
    }
}
