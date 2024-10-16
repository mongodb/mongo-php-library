<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $gte expression
 */
class GteOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                qty: 1,
                qtyGte250: Expression::gte(
                    Expression::numberFieldPath('qty'),
                    250,
                ),
                _id: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::GteExample, $pipeline);
    }
}
