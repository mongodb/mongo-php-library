<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $lastN expression
 */
class LastNOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                lastScores: Expression::lastN(n: 3, input: Expression::arrayFieldPath('score')),
            ),
        );

        $this->assertSamePipeline(Pipelines::LastNExample, $pipeline);
    }

    public function testUsingLastNAsAnAggregationExpression(): void
    {
        $pipeline = new Pipeline(
            Stage::documents([
                [
                    'array' => [10, 20, 30, 40],
                ],
            ]),
            Stage::project(
                lastThreeElements: Expression::lastN(input: Expression::arrayFieldPath('array'), n: 3),
            ),
        );

        $this->assertSamePipeline(Pipelines::LastNUsingLastNAsAnAggregationExpression, $pipeline);
    }
}
