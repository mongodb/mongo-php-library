<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $sigmoid expression
 */
class SigmoidOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::set(
                scaled: Expression::sigmoid(Expression::doubleFieldPath('score')),
            ),
        );

        $this->assertSamePipeline(Pipelines::SigmoidExample, $pipeline);
    }
}
