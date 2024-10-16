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
 * Test $replaceWith stage
 */
class ReplaceWithStageTest extends PipelineTestCase
{
    public function testADocumentNestedInAnArray(): void
    {
        $pipeline = new Pipeline(
            Stage::unwind(Expression::arrayFieldPath('grades')),
            Stage::match(
                ...['grades.grade' => Query::gte(90)],
            ),
            Stage::replaceWith(Expression::objectFieldPath('grades')),
        );

        $this->assertSamePipeline(Pipelines::ReplaceWithADocumentNestedInAnArray, $pipeline);
    }

    public function testANewDocumentCreatedFromROOTAndADefaultDocument(): void
    {
        $pipeline = new Pipeline(
            Stage::replaceWith(
                Expression::mergeObjects(
                    object(_id: '', name: '', email: '', cell: '', home: ''),
                    Expression::variable('ROOT'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReplaceWithANewDocumentCreatedFromROOTAndADefaultDocument, $pipeline);
    }

    public function testANewlyCreatedDocument(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                status: 'C',
            ),
            Stage::replaceWith(
                object(
                    _id: Expression::objectFieldPath('_id'),
                    item: Expression::fieldPath('item'),
                    amount: Expression::multiply(
                        Expression::numberFieldPath('price'),
                        Expression::numberFieldPath('quantity'),
                    ),
                    status: 'Complete',
                    asofDate: Expression::variable('NOW'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReplaceWithANewlyCreatedDocument, $pipeline);
    }

    public function testAnEmbeddedDocumentField(): void
    {
        $pipeline = new Pipeline(
            Stage::replaceWith(
                Expression::mergeObjects(
                    object(dogs: 0, cats: 0, birds: 0, fish: 0),
                    Expression::objectFieldPath('pets'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ReplaceWithAnEmbeddedDocumentField, $pipeline);
    }
}
