<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $first expression
 */
class FirstOperatorTest extends PipelineTestCase
{
    public function testUseInAddFieldsStage(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                firstItem: Expression::first(Expression::fieldPath('items')),
            ),
        );

        $this->assertSamePipeline(Pipelines::FirstUseInAddFieldsStage, $pipeline);
    }
}
