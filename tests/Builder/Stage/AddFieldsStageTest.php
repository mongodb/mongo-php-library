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
    public function testAddingFieldsToAnEmbeddedDocument(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                ...['specs.fuel_type' => 'unleaded'],
            ),
        );

        $this->assertSamePipeline(Pipelines::AddFieldsAddingFieldsToAnEmbeddedDocument, $pipeline);
    }

    public function testUsingTwoAddFieldsStages(): void
    {
        $this->markTestSkipped('$sum must accept arrayFieldPath and render it as a single value: https://jira.mongodb.org/browse/PHPLIB-1287');

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
