<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $trunc expression
 */
class TruncOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                truncatedValue: Expression::trunc(
                    Expression::numberFieldPath('value'),
                    1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TruncExample, $pipeline);
    }
}
