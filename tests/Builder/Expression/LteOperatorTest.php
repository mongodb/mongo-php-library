<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $lte expression
 */
class LteOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                qty: 1,
                qtyLte250: Expression::lte(
                    Expression::numberFieldPath('qty'),
                    250,
                ),
                _id: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::LteExample, $pipeline);
    }
}
