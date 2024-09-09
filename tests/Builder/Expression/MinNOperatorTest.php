<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $minN expression
 */
class MinNOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                minScores: Expression::minN(
                    n: 2,
                    input: Expression::arrayFieldPath('score'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MinNExample, $pipeline);
    }
}
