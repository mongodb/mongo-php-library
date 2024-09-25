<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $cos expression
 */
class CosOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                side_a: Expression::multiply(
                    Expression::cos(
                        Expression::degreesToRadians(
                            Expression::numberFieldPath('angle_a'),
                        ),
                    ),
                    Expression::numberFieldPath('hypotenuse'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::CosExample, $pipeline);
    }
}
