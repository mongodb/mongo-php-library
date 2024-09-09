<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $ne query
 */
class NeOperatorTest extends PipelineTestCase
{
    public function testMatchDocumentFields(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                quantity: Query::ne(20),
            ),
        );

        $this->assertSamePipeline(Pipelines::NeMatchDocumentFields, $pipeline);
    }
}
