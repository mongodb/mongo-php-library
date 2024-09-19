<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $asinh expression
 */
class AsinhOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                ...[
                    'y-coordinate' => Expression::radiansToDegrees(
                        Expression::asinh(
                            Expression::numberFieldPath('x-coordinate'),
                        ),
                    ),
                ],
            ),
        );

        $this->assertSamePipeline(Pipelines::AsinhExample, $pipeline);
    }
}
