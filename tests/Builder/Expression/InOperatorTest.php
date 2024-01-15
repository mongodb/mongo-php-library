<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $in expression
 */
class InOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                ...[
                    'store location' => Expression::fieldPath('location'),
                    'has bananas' => Expression::in(
                        'bananas',
                        Expression::arrayFieldPath('in_stock'),
                    ),
                ],
            ),
        );

        $this->assertSamePipeline(Pipelines::InExample, $pipeline);
    }
}
