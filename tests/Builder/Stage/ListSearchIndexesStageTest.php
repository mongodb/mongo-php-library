<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $listSearchIndexes stage
 */
class ListSearchIndexesStageTest extends PipelineTestCase
{
    public function testReturnASingleSearchIndexById(): void
    {
        $pipeline = new Pipeline(
            Stage::listSearchIndexes(
                id: '6524096020da840844a4c4a7',
            ),
        );

        $this->assertSamePipeline(Pipelines::ListSearchIndexesReturnASingleSearchIndexById, $pipeline);
    }

    public function testReturnASingleSearchIndexByName(): void
    {
        $pipeline = new Pipeline(
            Stage::listSearchIndexes(
                name: 'synonym-mappings',
            ),
        );

        $this->assertSamePipeline(Pipelines::ListSearchIndexesReturnASingleSearchIndexByName, $pipeline);
    }

    public function testReturnAllSearchIndexes(): void
    {
        $pipeline = new Pipeline(
            Stage::listSearchIndexes(),
        );

        $this->assertSamePipeline(Pipelines::ListSearchIndexesReturnAllSearchIndexes, $pipeline);
    }
}
