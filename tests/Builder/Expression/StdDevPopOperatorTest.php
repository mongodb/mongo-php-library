<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $stdDevPop expression
 */
class StdDevPopOperatorTest extends PipelineTestCase
{
    public function testUseInProjectStage(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                stdDev: Expression::stdDevPop(
                    Expression::numberFieldPath('scores.score'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::StdDevPopUseInProjectStage, $pipeline);
    }
}
