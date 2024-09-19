<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $avg expression
 */
class AvgOperatorTest extends PipelineTestCase
{
    public function testUseInProjectStage(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                quizAvg: Expression::avg(
                    Expression::numberFieldPath('quizzes'),
                ),
                labAvg: Expression::avg(
                    Expression::numberFieldPath('labs'),
                ),
                examAvg: Expression::avg(
                    Expression::numberFieldPath('final'),
                    Expression::numberFieldPath('midterm'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AvgUseInProjectStage, $pipeline);
    }
}
