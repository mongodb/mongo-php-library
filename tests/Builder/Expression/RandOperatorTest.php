<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $rand expression
 */
class RandOperatorTest extends PipelineTestCase
{
    public function testGenerateRandomDataPoints(): void
    {
        $pipeline = new Pipeline(
            Stage::set(
                amount: Expression::multiply(
                    Expression::rand(),
                    100,
                ),
            ),
            Stage::set(
                amount: Expression::floor(
                    Expression::numberFieldPath('amount'),
                ),
            ),
            Stage::merge('donors'),
        );

        $this->assertSamePipeline(Pipelines::RandGenerateRandomDataPoints, $pipeline);
    }

    public function testSelectRandomItemsFromACollection(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                district: 3,
            ),
            Stage::match(
                Query::expr(
                    Expression::lt(
                        0.5,
                        Expression::rand(),
                    ),
                ),
            ),
            Stage::project(
                _id: 0,
                name: 1,
                registered: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::RandSelectRandomItemsFromACollection, $pipeline);
    }
}
