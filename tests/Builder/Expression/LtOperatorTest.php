<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $lt expression
 */
class LtOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                qty: 1,
                qtyLt250: Expression::lt(
                    Expression::numberFieldPath('qty'),
                    250,
                ),
                _id: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::LtExample, $pipeline);
    }
}
