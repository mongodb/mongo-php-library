<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $last expression
 */
class LastOperatorTest extends PipelineTestCase
{
    public function testUseInAddFieldsStage(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                lastItem: Expression::last(Expression::fieldPath('items')),
            ),
        );

        $this->assertSamePipeline(Pipelines::LastUseInAddFieldsStage, $pipeline);
    }
}
