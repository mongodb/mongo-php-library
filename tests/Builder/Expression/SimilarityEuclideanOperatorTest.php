<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $similarityEuclidean expression
 */
class SimilarityEuclideanOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                raw: Expression::similarityEuclidean(['$a', '$b']),
                normalized: Expression::similarityEuclidean(vectors: ['$a', '$b'], score: true),
            ),
        );

        $this->assertSamePipeline(Pipelines::SimilarityEuclideanExample, $pipeline);
    }
}
