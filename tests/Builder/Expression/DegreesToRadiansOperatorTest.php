<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $degreesToRadians expression
 */
class DegreesToRadiansOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                angle_a_rad: Expression::degreesToRadians(
                    Expression::numberFieldPath('angle_a'),
                ),
                angle_b_rad: Expression::degreesToRadians(
                    Expression::numberFieldPath('angle_b'),
                ),
                angle_c_rad: Expression::degreesToRadians(
                    Expression::numberFieldPath('angle_c'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DegreesToRadiansExample, $pipeline);
    }
}
