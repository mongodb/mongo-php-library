<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $min expression
 */
class MinOperatorTest extends PipelineTestCase
{
    public function testUseInProjectStage(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                quizMin: Expression::min(
                    Expression::numberFieldPath('quizzes'),
                ),
                labMin: Expression::min(
                    Expression::numberFieldPath('labs'),
                ),
                examMin: Expression::min(
                    Expression::numberFieldPath('final'),
                    Expression::numberFieldPath('midterm'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MinUseInProjectStage, $pipeline);
    }
}
