<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $tanh expression
 */
class TanhOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                tanh_output:
                Expression::tanh(
                    Expression::degreesToRadians(
                        Expression::numberFieldPath('angle'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TanhExample, $pipeline);
    }
}
