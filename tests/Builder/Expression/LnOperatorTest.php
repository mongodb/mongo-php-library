<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $ln expression
 */
class LnOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                x: Expression::numberFieldPath('year'),
                y: Expression::ln(
                    Expression::numberFieldPath('sales'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::LnExample, $pipeline);
    }
}
