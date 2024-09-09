<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $set stage
 */
class SetStageTest extends PipelineTestCase
{
    public function testAddElementToAnArray(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                _id: 1,
            ),
            Stage::set(
                homework: Expression::concatArrays(
                    Expression::arrayFieldPath('homework'),
                    [7],
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetAddElementToAnArray, $pipeline);
    }

    public function testAddingFieldsToAnEmbeddedDocument(): void
    {
        $pipeline = new Pipeline(
            Stage::set(
                ...['specs.fuel_type' => 'unleaded'],
            ),
        );

        $this->assertSamePipeline(Pipelines::SetAddingFieldsToAnEmbeddedDocument, $pipeline);
    }

    public function testCreatingANewFieldWithExistingFields(): void
    {
        $pipeline = new Pipeline(
            Stage::set(
                quizAverage: Expression::avg(
                    Expression::numberFieldPath('quiz'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetCreatingANewFieldWithExistingFields, $pipeline);
    }

    public function testOverwritingAnExistingField(): void
    {
        $pipeline = new Pipeline(
            Stage::set(cats: 20),
        );

        $this->assertSamePipeline(Pipelines::SetOverwritingAnExistingField, $pipeline);
    }

    public function testUsingTwoSetStages(): void
    {
        $pipeline = new Pipeline(
            Stage::set(
                totalHomework: Expression::sum(
                    Expression::arrayFieldPath('homework'),
                ),
                totalQuiz: Expression::sum(
                    Expression::arrayFieldPath('quiz'),
                ),
            ),
            Stage::set(
                totalScore: Expression::add(
                    Expression::numberFieldPath('totalHomework'),
                    Expression::numberFieldPath('totalQuiz'),
                    Expression::numberFieldPath('extraCredit'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SetUsingTwoSetStages, $pipeline);
    }
}
