<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $sinh expression
 */
class SinhOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                sinh_output: Expression::sinh(
                    Expression::degreesToRadians(
                        Expression::numberFieldPath('angle'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SinhExample, $pipeline);
    }
}
