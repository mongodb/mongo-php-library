<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $atan expression
 */
class AtanOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                angle_a: Expression::radiansToDegrees(
                    Expression::atan(
                        Expression::divide(
                            Expression::numberFieldPath('side_b'),
                            Expression::numberFieldPath('side_a'),
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AtanExample, $pipeline);
    }
}
