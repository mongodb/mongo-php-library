<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $nin query
 */
class NinOperatorTest extends PipelineTestCase
{
    public function testSelectOnElementsNotInAnArray(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                tags: Query::nin(['school']),
            ),
        );

        $this->assertSamePipeline(Pipelines::NinSelectOnElementsNotInAnArray, $pipeline);
    }

    public function testSelectOnUnmatchingDocuments(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                quantity: Query::nin([5, 15]),
            ),
        );

        $this->assertSamePipeline(Pipelines::NinSelectOnUnmatchingDocuments, $pipeline);
    }
}
