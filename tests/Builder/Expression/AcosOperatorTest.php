<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $acos expression
 */
class AcosOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                angle_a: Expression::radiansToDegrees(
                    Expression::acos(
                        Expression::divide(
                            Expression::numberFieldPath('side_b'),
                            Expression::numberFieldPath('hypotenuse'),
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AcosExample, $pipeline);
    }
}
