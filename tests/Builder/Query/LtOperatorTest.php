<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $lt query
 */
class LtOperatorTest extends PipelineTestCase
{
    public function testMatchDocumentFields(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                qty: Query::lt(20),
            ),
        );

        $this->assertSamePipeline(Pipelines::LtMatchDocumentFields, $pipeline);
    }
}
