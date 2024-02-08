<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $toLong expression
 */
class ToLongOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                convertedQty: Expression::toLong(
                    Expression::fieldPath('qty'),
                ),
            ),
            Stage::sort(
                convertedQty: Sort::Desc,
            ),
        );

        $this->assertSamePipeline(Pipelines::ToLongExample, $pipeline);
    }
}
