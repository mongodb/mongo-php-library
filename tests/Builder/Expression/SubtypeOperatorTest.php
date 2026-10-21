<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $subtype expression
 */
class SubtypeOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                result: Expression::subtype(
                    Expression::binDataFieldPath('myBinDataField'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SubtypeExample, $pipeline);
    }
}
