<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $strcasecmp expression
 */
class StrcasecmpOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                item: 1,
                comparisonResult: Expression::strcasecmp(
                    Expression::stringFieldPath('quarter'),
                    '13q4',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::StrcasecmpExample, $pipeline);
    }
}
