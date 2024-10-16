<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $strLenCP expression
 */
class StrLenCPOperatorTest extends PipelineTestCase
{
    public function testSingleByteAndMultibyteCharacterSet(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                name: 1,
                length: Expression::strLenCP(
                    Expression::stringFieldPath('name'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::StrLenCPSingleByteAndMultibyteCharacterSet, $pipeline);
    }
}
