<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $tan expression
 */
class TanOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                side_b: Expression::multiply(
                    Expression::tan(
                        Expression::degreesToRadians(
                            Expression::numberFieldPath('angle_a'),
                        ),
                    ),
                    Expression::numberFieldPath('side_a'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TanExample, $pipeline);
    }
}
