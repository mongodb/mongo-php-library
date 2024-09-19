<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $mod expression
 */
class ModOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                remainder: Expression::mod(
                    Expression::intFieldPath('hours'),
                    Expression::intFieldPath('tasks'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ModExample, $pipeline);
    }
}
