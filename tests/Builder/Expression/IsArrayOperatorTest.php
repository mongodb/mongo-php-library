<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $isArray expression
 */
class IsArrayOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                items: Expression::cond(
                    if: Expression::and(
                        Expression::isArray(Expression::fieldPath('instock')),
                        Expression::isArray(Expression::fieldPath('ordered')),
                    ),
                    then: Expression::concatArrays(
                        Expression::arrayFieldPath('instock'),
                        Expression::arrayFieldPath('ordered'),
                    ),
                    else: 'One or more fields is not an array.',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::IsArrayExample, $pipeline);
    }
}
