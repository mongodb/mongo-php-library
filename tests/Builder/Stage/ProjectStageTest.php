<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $project stage
 */
class ProjectStageTest extends PipelineTestCase
{
    public function testConditionallyExcludeFields(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                // Array unpacking is necessary for name that are not valid argument names,
                // in PHP, they must be defined before named arguments.
                ...[
                    'author.first' => 1,
                    'author.last' => 1,
                    'author.middle' => Expression::cond(
                        if: Expression::eq(
                            '',
                            Expression::stringFieldPath('author.middle'),
                        ),
                        then: Expression::variable('REMOVE'),
                        else: Expression::stringFieldPath('author.middle'),
                    ),
                ],
                title: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::ProjectConditionallyExcludeFields, $pipeline);
    }

    public function testExcludeFieldsFromEmbeddedDocuments(): void
    {
        $pipeline = new Pipeline(
            // Both stages are equivalents
            Stage::project(
                ...['author.first' => 0],
                ...['lastModified' => 0],
            ),
            Stage::project(
                author: object(first: 0),
                lastModified: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::ProjectExcludeFieldsFromEmbeddedDocuments, $pipeline);
    }

    public function testExcludeFieldsFromOutputDocuments(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                lastModified: 0,
            ),
        );

        $this->assertSamePipeline(Pipelines::ProjectExcludeFieldsFromOutputDocuments, $pipeline);
    }

    public function testIncludeComputedFields(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                title: 1,
                isbn: object(
                    prefix: Expression::substr(
                        Expression::stringFieldPath('isbn'),
                        0,
                        3,
                    ),
                    group: Expression::substr(
                        Expression::stringFieldPath('isbn'),
                        3,
                        2,
                    ),
                    publisher: Expression::substr(
                        Expression::stringFieldPath('isbn'),
                        5,
                        4,
                    ),
                    title: Expression::substr(
                        Expression::stringFieldPath('isbn'),
                        9,
                        3,
                    ),
                    checkDigit: Expression::substr(
                        Expression::stringFieldPath('isbn'),
                        12,
                        1,
                    ),
                ),
                lastName: Expression::stringFieldPath('author.last'),
                copiesSold: Expression::intFieldPath('copies'),
            ),
        );

        $this->assertSamePipeline(Pipelines::ProjectIncludeComputedFields, $pipeline);
    }

    public function testIncludeSpecificFieldsFromEmbeddedDocuments(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                ...['stop.title' => 1],
            ),
            Stage::project(
                stop: object(title: 1),
            ),
        );

        $this->assertSamePipeline(Pipelines::ProjectIncludeSpecificFieldsFromEmbeddedDocuments, $pipeline);
    }

    public function testIncludeSpecificFieldsInOutputDocuments(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                title: 1,
                author: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::ProjectIncludeSpecificFieldsInOutputDocuments, $pipeline);
    }

    public function testProjectNewArrayFields(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                myArray: [
                    Expression::fieldPath('x'),
                    Expression::fieldPath('y'),
                ],
            ),
        );

        $this->assertSamePipeline(Pipelines::ProjectProjectNewArrayFields, $pipeline);
    }

    public function testSuppressIdFieldInTheOutputDocuments(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                title: 1,
                author: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::ProjectSuppressIdFieldInTheOutputDocuments, $pipeline);
    }
}
