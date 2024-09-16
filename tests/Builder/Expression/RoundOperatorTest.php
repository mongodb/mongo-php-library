<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $round expression
 */
class RoundOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                roundedValue: Expression::round(
                    Expression::intFieldPath('value'),
                    1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RoundExample, $pipeline);
    }

    public function testRoundAverageRating(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                roundedAverageRating: Expression::avg(
                    Expression::round(
                        Expression::avg(
                            Expression::doubleFieldPath('averageRating'),
                        ),
                        2,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RoundRoundAverageRating, $pipeline);
    }
}
