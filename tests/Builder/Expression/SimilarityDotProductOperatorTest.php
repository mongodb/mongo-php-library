<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $similarityDotProduct expression
 */
class SimilarityDotProductOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                raw: Expression::similarityDotProduct(['$a', '$b']),
                normalized: Expression::similarityDotProduct(vectors: ['$a', '$b'], score: true),
            ),
        );

        $this->assertSamePipeline(Pipelines::SimilarityDotProductExample, $pipeline);
    }
}
