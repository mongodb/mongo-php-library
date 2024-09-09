<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $indexOfBytes expression
 */
class IndexOfBytesOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                byteLocation: Expression::indexOfBytes(
                    Expression::stringFieldPath('item'),
                    'foo',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::IndexOfBytesExample, $pipeline);
    }
}
