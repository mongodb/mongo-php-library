<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $pow expression
 */
class PowOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                variance: Expression::pow(
                    Expression::stdDevPop(
                        Expression::intFieldPath('scores.score'),
                    ),
                    2,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::PowExample, $pipeline);
    }
}
