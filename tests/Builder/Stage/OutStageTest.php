<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $out stage
 */
class OutStageTest extends PipelineTestCase
{
    public function testOutputToADifferentDatabase(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::stringFieldPath('author'),
                books: Accumulator::push(
                    Expression::stringFieldPath('title'),
                ),
            ),
            Stage::out(
                object(
                    db: 'reporting',
                    coll: 'authors',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::OutOutputToADifferentDatabase, $pipeline);
    }

    public function testOutputToSameDatabase(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::stringFieldPath('author'),
                books: Accumulator::push(
                    Expression::stringFieldPath('title'),
                ),
            ),
            Stage::out('authors'),
        );

        $this->assertSamePipeline(Pipelines::OutOutputToSameDatabase, $pipeline);
    }
}
