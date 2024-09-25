<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $size expression
 */
class SizeOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                numberOfColors: Expression::cond(
                    if: Expression::isArray(
                        Expression::fieldPath('colors'),
                    ),
                    then: Expression::size(
                        Expression::arrayFieldPath('colors'),
                    ),
                    else: 'NA',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SizeExample, $pipeline);
    }
}
