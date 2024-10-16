<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $substrBytes expression
 */
class SubstrBytesOperatorTest extends PipelineTestCase
{
    public function testSingleByteAndMultibyteCharacterSet(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                name: 1,
                menuCode: Expression::substrBytes(
                    Expression::stringFieldPath('name'),
                    0,
                    3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SubstrBytesSingleByteAndMultibyteCharacterSet, $pipeline);
    }

    public function testSingleByteCharacterSet(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                yearSubstring: Expression::substrBytes(
                    Expression::stringFieldPath('quarter'),
                    0,
                    2,
                ),
                quarterSubtring: Expression::substrBytes(
                    Expression::stringFieldPath('quarter'),
                    2,
                    Expression::subtract(
                        Expression::strLenBytes(
                            Expression::stringFieldPath('quarter'),
                        ),
                        2,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SubstrBytesSingleByteCharacterSet, $pipeline);
    }
}
