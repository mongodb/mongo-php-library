<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $switch expression
 */
class SwitchOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                name: 1,
                summary: Expression::switch(
                    branches: [
                        Expression::case(
                            case: Expression::gte(
                                Expression::avg(
                                    Expression::intFieldPath('scores'),
                                ),
                                90,
                            ),
                            then: 'Doing great!',
                        ),
                        Expression::case(
                            case:Expression::and(
                                Expression::gte(
                                    Expression::avg(
                                        Expression::intFieldPath('scores'),
                                    ),
                                    80,
                                ),
                                Expression::lt(
                                    Expression::avg(
                                        Expression::intFieldPath('scores'),
                                    ),
                                    90,
                                ),
                            ),
                            then: 'Doing pretty well.',
                        ),
                        Expression::case(
                            case: Expression::lt(
                                Expression::avg(
                                    Expression::intFieldPath('scores'),
                                ),
                                80,
                            ),
                            then: 'Needs improvement.',
                        ),
                    ],
                    default: 'No scores found.',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SwitchExample, $pipeline);
    }
}
