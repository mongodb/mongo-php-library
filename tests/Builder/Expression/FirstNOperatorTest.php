<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $firstN expression
 */
class FirstNOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                firstScores: Expression::firstN(n: 3, input: Expression::arrayFieldPath('score')),
            ),
        );

        $this->assertSamePipeline(Pipelines::FirstNExample, $pipeline);
    }

    public function testUsingFirstNAsAnAggregationExpression(): void
    {
        $pipeline = new Pipeline(
            Stage::documents([
                object(
                    array: [10, 20, 30, 40],
                ),
            ]),
            Stage::project(
                firstThreeElements: Expression::firstN(
                    input: Expression::arrayFieldPath('array'),
                    n: 3,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FirstNUsingFirstNAsAnAggregationExpression, $pipeline);
    }
}
