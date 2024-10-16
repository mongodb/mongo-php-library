<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $size query
 */
class SizeOperatorTest extends PipelineTestCase
{
    public function testQueryAnArrayByArrayLength(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                tags: Query::size(3),
            ),
        );

        $this->assertSamePipeline(Pipelines::SizeQueryAnArrayByArrayLength, $pipeline);
    }
}
