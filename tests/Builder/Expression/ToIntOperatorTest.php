<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $toInt expression
 */
class ToIntOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                convertedQty: Expression::toInt(
                    Expression::fieldPath('qty'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ToIntExample, $pipeline);
    }
}
