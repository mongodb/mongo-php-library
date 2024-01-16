<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $exists query
 */
class ExistsOperatorTest extends PipelineTestCase
{
    public function testExistsAndNotEqualTo(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                qty: [
                    Query::exists(true),
                    Query::nin([5, 15]),
                ],
            ),
        );

        $this->assertSamePipeline(Pipelines::ExistsExistsAndNotEqualTo, $pipeline);
    }

    public function testNullValues(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                qty: [
                    Query::exists(true),
                ],
            ),
        );

        $this->assertSamePipeline(Pipelines::ExistsNullValues, $pipeline);
    }
}
