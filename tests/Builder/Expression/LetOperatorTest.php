<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $let expression
 */
class LetOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                finalTotal: Expression::let(
                    vars: object(
                        total: Expression::add(
                            Expression::numberFieldPath('price'),
                            Expression::numberFieldPath('tax'),
                        ),
                        discounted: Expression::cond(
                            if: Expression::boolFieldPath('applyDiscount'),
                            then: 0.9,
                            else: 1,
                        ),
                    ),
                    in: Expression::multiply(
                        Expression::variable('total'),
                        Expression::variable('discounted'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::LetExample, $pipeline);
    }
}
