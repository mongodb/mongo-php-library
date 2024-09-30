<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $type expression
 */
class TypeOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                a: Expression::type(
                    Expression::fieldPath('a'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TypeExample, $pipeline);
    }
}
