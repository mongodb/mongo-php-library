<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $strLenBytes expression
 */
class StrLenBytesOperatorTest extends PipelineTestCase
{
    public function testSingleByteAndMultibyteCharacterSet(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                name: 1,
                length: Expression::strLenBytes(
                    Expression::stringFieldPath('name'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::StrLenBytesSingleByteAndMultibyteCharacterSet, $pipeline);
    }
}
