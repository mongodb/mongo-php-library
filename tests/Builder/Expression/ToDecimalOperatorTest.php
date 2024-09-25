<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $toDecimal expression
 */
class ToDecimalOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                convertedPrice: Expression::toDecimal(
                    Expression::fieldPath('price'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ToDecimalExample, $pipeline);
    }
}
