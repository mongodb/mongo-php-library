<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

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
                object(
                    convertedQty: -1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ToLongExample, $pipeline);
    }
}
