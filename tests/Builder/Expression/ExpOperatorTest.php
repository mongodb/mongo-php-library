<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $exp expression
 */
class ExpOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                effectiveRate: Expression::subtract(
                    Expression::exp(
                        Expression::numberFieldPath('interestRate'),
                    ),
                    1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ExpExample, $pipeline);
    }
}
