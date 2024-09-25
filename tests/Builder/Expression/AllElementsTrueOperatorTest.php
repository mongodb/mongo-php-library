<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $allElementsTrue expression
 */
class AllElementsTrueOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                responses: 1,
                isAllTrue: Expression::allElementsTrue(
                    Expression::arrayFieldPath('responses'),
                ),
                _id: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::AllElementsTrueExample, $pipeline);
    }
}
