<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $max expression
 */
class MaxOperatorTest extends PipelineTestCase
{
    public function testUseInProjectStage(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                quizMax: Expression::max(
                    Expression::numberFieldPath('quizzes'),
                ),
                labMax: Expression::max(
                    Expression::numberFieldPath('labs'),
                ),
                examMax: Expression::max(
                    Expression::numberFieldPath('final'),
                    Expression::numberFieldPath('midterm'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MaxUseInProjectStage, $pipeline);
    }
}
