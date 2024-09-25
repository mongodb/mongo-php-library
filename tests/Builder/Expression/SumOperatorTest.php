<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $sum expression
 */
class SumOperatorTest extends PipelineTestCase
{
    public function testUseInProjectStage(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                quizTotal: Expression::sum(
                    Expression::intFieldPath('quizzes'),
                ),
                labTotal: Expression::sum(
                    Expression::longFieldPath('labs'),
                ),
                examTotal: Expression::sum(
                    Expression::doubleFieldPath('final'),
                    Expression::decimalFieldPath('midterm'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SumUseInProjectStage, $pipeline);
    }
}
