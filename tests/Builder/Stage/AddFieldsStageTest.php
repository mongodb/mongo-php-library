<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $addFields stage
 */
class AddFieldsStageTest extends PipelineTestCase
{
    public function testAddElementToAnArray(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                _id: 1,
            ),
            Stage::addFields(
                homework: Expression::concatArrays(
                    Expression::arrayFieldPath('homework'),
                    [7],
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AddFieldsAddElementToAnArray, $pipeline);
    }

    public function testAddingFieldsToAnEmbeddedDocument(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                ...['specs.fuel_type' => 'unleaded'],
            ),
        );

        $this->assertSamePipeline(Pipelines::AddFieldsAddingFieldsToAnEmbeddedDocument, $pipeline);
    }

    public function testOverwritingAnExistingField(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                cats: 20,
            ),
        );

        $this->assertSamePipeline(Pipelines::AddFieldsOverwritingAnExistingField, $pipeline);
    }

    public function testUsingTwoAddFieldsStages(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                totalHomework: Expression::sum(Expression::fieldPath('homework')),
                totalQuiz: Expression::sum(Expression::fieldPath('quiz')),
            ),
            Stage::addFields(
                totalScore: Expression::add(
                    Expression::fieldPath('totalHomework'),
                    Expression::fieldPath('totalQuiz'),
                    Expression::fieldPath('extraCredit'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AddFieldsUsingTwoAddFieldsStages, $pipeline);
    }
}
