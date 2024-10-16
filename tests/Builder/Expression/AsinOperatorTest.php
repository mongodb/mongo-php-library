<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $asin expression
 */
class AsinOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                angle_a: Expression::radiansToDegrees(
                    Expression::asin(
                        Expression::divide(
                            Expression::numberFieldPath('side_a'),
                            Expression::numberFieldPath('hypotenuse'),
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AsinExample, $pipeline);
    }
}
