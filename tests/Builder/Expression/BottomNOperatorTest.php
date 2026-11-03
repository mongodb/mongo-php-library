<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $bottomN expression
 */
class BottomNOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                bottomScores: Expression::bottomN(
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

        $this->assertSamePipeline(Pipelines::BottomNExample, $pipeline);
    }
}
