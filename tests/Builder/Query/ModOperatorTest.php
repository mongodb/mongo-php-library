<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $mod query
 */
class ModOperatorTest extends PipelineTestCase
{
    public function testFloatingPointArguments(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                qty: Query::mod(4.0, 0),
            ),
            Stage::match(
                qty: Query::mod(4.5, 0),
            ),
            Stage::match(
                qty: Query::mod(4.99, 0),
            ),
        );

        $this->assertSamePipeline(Pipelines::ModFloatingPointArguments, $pipeline);
    }

    public function testUseModToSelectDocuments(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                qty: Query::mod(4, 0),
            ),
        );

        $this->assertSamePipeline(Pipelines::ModUseModToSelectDocuments, $pipeline);
    }
}
