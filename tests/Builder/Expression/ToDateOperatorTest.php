<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $toDate expression
 */
class ToDateOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                convertedDate: Expression::toDate(
                    Expression::fieldPath('order_date'),
                ),
            ),
            Stage::sort(
                convertedDate: Sort::Asc,
            ),
        );

        $this->assertSamePipeline(Pipelines::ToDateExample, $pipeline);
    }
}
