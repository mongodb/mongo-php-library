<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $bsonSize expression
 */
class BsonSizeOperatorTest extends PipelineTestCase
{
    public function testReturnCombinedSizeOfAllDocumentsInACollection(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: null,
                combined_object_size: Accumulator::sum(
                    Expression::bsonSize(
                        Expression::variable('ROOT'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BsonSizeReturnCombinedSizeOfAllDocumentsInACollection, $pipeline);
    }

    public function testReturnDocumentWithLargestSpecifiedField(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                name: Expression::stringFieldPath('name'),
                task_object_size: Expression::bsonSize(
                    Expression::objectFieldPath('current_task'),
                ),
            ),
            Stage::sort(
                object(task_object_size: -1),
            ),
            Stage::limit(1),
        );

        $this->assertSamePipeline(Pipelines::BsonSizeReturnDocumentWithLargestSpecifiedField, $pipeline);
    }

    public function testReturnSizesOfDocuments(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                name: 1,
                object_size: Expression::bsonSize(
                    Expression::variable('ROOT'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BsonSizeReturnSizesOfDocuments, $pipeline);
    }
}
