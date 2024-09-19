<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $sin expression
 */
class SinOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                side_b: Expression::multiply(
                    Expression::sin(
                        Expression::degreesToRadians(
                            Expression::numberFieldPath('angle_a'),
                        ),
                    ),
                    Expression::numberFieldPath('hypotenuse'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SinExample, $pipeline);
    }
}
