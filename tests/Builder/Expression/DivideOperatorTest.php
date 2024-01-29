<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $divide expression
 */
class DivideOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                city: 1,
                workdays: Expression::divide(
                    Expression::numberFieldPath('hours'),
                    8,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DivideExample, $pipeline);
    }
}
