<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $createObjectId expression
 */
class CreateObjectIdOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                objectId: Expression::createObjectId(),
            ),
        );

        $this->assertSamePipeline(Pipelines::CreateObjectIdExample, $pipeline);
    }
}
