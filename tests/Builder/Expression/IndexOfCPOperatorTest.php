<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $indexOfCP expression
 */
class IndexOfCPOperatorTest extends PipelineTestCase
{
    public function testExamples(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                cpLocation: Expression::indexOfCP(
                    Expression::stringFieldPath('item'),
                    'foo',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::IndexOfCPExamples, $pipeline);
    }
}
