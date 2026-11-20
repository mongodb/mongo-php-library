<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $similarityCosine expression
 */
class SimilarityCosineOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                raw: Expression::similarityCosine(['$a', '$b']),
                normalized: Expression::similarityCosine(vectors: ['$a', '$b'], score: true),
            ),
        );

        $this->assertSamePipeline(Pipelines::SimilarityCosineExample, $pipeline);
    }
}
