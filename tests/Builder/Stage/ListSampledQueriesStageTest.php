<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $listSampledQueries stage
 */
class ListSampledQueriesStageTest extends PipelineTestCase
{
    public function testListSampledQueriesForASpecificCollection(): void
    {
        $pipeline = new Pipeline(
            Stage::listSampledQueries(
                namespace: 'social.post',
            ),
        );

        $this->assertSamePipeline(Pipelines::ListSampledQueriesListSampledQueriesForASpecificCollection, $pipeline);
    }

    public function testListSampledQueriesForAllCollections(): void
    {
        $pipeline = new Pipeline(
            Stage::listSampledQueries(),
        );

        $this->assertSamePipeline(Pipelines::ListSampledQueriesListSampledQueriesForAllCollections, $pipeline);
    }
}
