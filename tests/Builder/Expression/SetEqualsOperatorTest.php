<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $setEquals expression
 */
class SetEqualsOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                cakes: 1,
                cupcakes: 1,
                sameFlavors: Expression::setEquals(
                    Expression::arrayFieldPath('cakes'),
                    Expression::arrayFieldPath('cupcakes'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetEqualsExample, $pipeline);
    }
}
