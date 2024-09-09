<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $sqrt expression
 */
class SqrtOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                distance: Expression::sqrt(
                    Expression::add(
                        Expression::pow(
                            Expression::subtract(
                                Expression::numberFieldPath('p2.y'),
                                Expression::numberFieldPath('p1.y'),
                            ),
                            2,
                        ),
                        Expression::pow(
                            Expression::subtract(
                                Expression::numberFieldPath('p2.x'),
                                Expression::numberFieldPath('p1.x'),
                            ),
                            2,
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SqrtExample, $pipeline);
    }
}
