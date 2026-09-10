<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $top expression
 */
class TopOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                topScore: Expression::top(
                    sortBy: object(score: -1),
                    output: [
                        Expression::fieldPath('playerId'),
                        Expression::fieldPath('score'),
                    ],
                    input: Expression::arrayFieldPath('results'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TopExample, $pipeline);
    }
}
