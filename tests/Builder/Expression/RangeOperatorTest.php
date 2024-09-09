<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $range expression
 */
class RangeOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                // fields order doesn't matter, array unpacking must be before named arguments
                ...[
                    'Rest stops' => Expression::range(
                        0,
                        Expression::intFieldPath('distance'),
                        25,
                    ),
                ],
                _id: 0,
                city: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::RangeExample, $pipeline);
    }
}
