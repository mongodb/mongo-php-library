<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $cmp expression
 */
class CmpOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                qty: 1,
                cmpTo250: Expression::cmp(
                    Expression::numberFieldPath('qty'),
                    250,
                ),
                _id: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::CmpExample, $pipeline);
    }
}
