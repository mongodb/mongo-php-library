<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $cosh expression
 */
class CoshOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                cosh_output: Expression::cosh(
                    Expression::degreesToRadians(
                        Expression::numberFieldPath('angle'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::CoshExample, $pipeline);
    }
}
