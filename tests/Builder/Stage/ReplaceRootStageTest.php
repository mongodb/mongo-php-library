<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $replaceRoot stage
 */
class ReplaceRootStageTest extends PipelineTestCase
{
    public function testWithADocumentNestedInAnArray(): void
    {
        $pipeline = new Pipeline(
            Stage::unwind(Expression::arrayFieldPath('grades')),
            Stage::match(
                ...['grades.grade' => Query::gte(90)],
            ),
            Stage::replaceRoot(Expression::objectFieldPath('grades')),
        );

        $this->assertSamePipeline(Pipelines::ReplaceRootWithADocumentNestedInAnArray, $pipeline);
    }

    public function testWithANewDocumentCreatedFromROOTAndADefaultDocument(): void
    {
        $pipeline = new Pipeline(
            Stage::replaceRoot(
                Expression::mergeObjects(
                    object(_id: '', name: '', email: '', cell: '', home: ''),
                    Expression::variable('ROOT'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReplaceRootWithANewDocumentCreatedFromROOTAndADefaultDocument, $pipeline);
    }

    public function testWithANewlyCreatedDocument(): void
    {
        $pipeline = new Pipeline(
            Stage::replaceRoot(
                object(
                    full_name: Expression::concat(
                        Expression::stringFieldPath('first_name'),
                        ' ',
                        Expression::stringFieldPath('last_name'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReplaceRootWithANewlyCreatedDocument, $pipeline);
    }

    public function testWithAnEmbeddedDocumentField(): void
    {
        $pipeline = new Pipeline(
            Stage::replaceRoot(
                Expression::mergeObjects(
                    object(dogs: 0, cats: 0, birds: 0, fish: 0),
                    Expression::objectFieldPath('pets'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReplaceRootWithAnEmbeddedDocumentField, $pipeline);
    }
}
