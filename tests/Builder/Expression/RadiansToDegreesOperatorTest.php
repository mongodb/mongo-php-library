<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $radiansToDegrees expression
 */
class RadiansToDegreesOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                angle_a_deg: Expression::radiansToDegrees(
                    Expression::numberFieldPath('angle_a'),
                ),
                angle_b_deg: Expression::radiansToDegrees(
                    Expression::numberFieldPath('angle_b'),
                ),
                angle_c_deg: Expression::radiansToDegrees(
                    Expression::numberFieldPath('angle_c'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::RadiansToDegreesExample, $pipeline);
    }
}
