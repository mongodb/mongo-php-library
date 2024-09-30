<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $arrayElemAt expression
 */
class ArrayElemAtOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                name: 1,
                first: Expression::arrayElemAt(
                    array: Expression::arrayFieldPath('favorites'),
                    idx: 0,
                ),
                last: Expression::arrayElemAt(
                    array: Expression::arrayFieldPath('favorites'),
                    idx: -1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ArrayElemAtExample, $pipeline);
    }
}
