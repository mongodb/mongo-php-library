<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $topN expression
 */
class TopNOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                topScores: Expression::topN(
                    n: 3,
                    sortBy: object(score: -1),
                    output: [
                        Expression::fieldPath('playerId'),
                        Expression::fieldPath('score'),
                    ],
                    input: Expression::arrayFieldPath('results'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TopNExample, $pipeline);
    }
}
