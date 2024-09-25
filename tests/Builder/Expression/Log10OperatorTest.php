<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $log10 expression
 */
class Log10OperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                pH: Expression::multiply(
                    -1,
                    Expression::log10(
                        Expression::numberFieldPath('H3O'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::Log10Example, $pipeline);
    }
}
