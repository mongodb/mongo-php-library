<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $unset stage
 */
class UnsetStageTest extends PipelineTestCase
{
    public function testRemoveASingleField(): void
    {
        $pipeline = new Pipeline(
            Stage::unset('copies'),
        );

        $this->assertSamePipeline(Pipelines::UnsetRemoveASingleField, $pipeline);
    }

    public function testRemoveEmbeddedFields(): void
    {
        $pipeline = new Pipeline(
            Stage::unset(
                'isbn',
                'author.first',
                'copies.warehouse',
            ),
        );

        $this->assertSamePipeline(Pipelines::UnsetRemoveEmbeddedFields, $pipeline);
    }

    public function testRemoveTopLevelFields(): void
    {
        $pipeline = new Pipeline(
            Stage::unset(
                'isbn',
                'copies',
            ),
        );

        $this->assertSamePipeline(Pipelines::UnsetRemoveTopLevelFields, $pipeline);
    }
}
