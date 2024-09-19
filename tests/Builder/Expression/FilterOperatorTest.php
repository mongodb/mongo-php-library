<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $filter expression
 */
class FilterOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                items: Expression::filter(
                    input: Expression::arrayFieldPath('items'),
                    as: 'item',
                    cond: Expression::gte(
                        Expression::variable('item.price'),
                        100,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FilterExample, $pipeline);
    }

    public function testLimitAsANumericExpression(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                items: Expression::filter(
                    input: Expression::arrayFieldPath('items'),
                    cond: Expression::lte(
                        Expression::variable('item.price'),
                        150,
                    ),
                    as: 'item',
                    limit: 2,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FilterLimitAsANumericExpression, $pipeline);
    }

    public function testLimitGreaterThanPossibleMatches(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                items: Expression::filter(
                    input: Expression::arrayFieldPath('items'),
                    cond: Expression::gte(
                        Expression::variable('item.price'),
                        100,
                    ),
                    as: 'item',
                    limit: 5,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FilterLimitGreaterThanPossibleMatches, $pipeline);
    }

    public function testUsingTheLimitField(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                items: Expression::filter(
                    input: Expression::arrayFieldPath('items'),
                    cond: Expression::gte(
                        Expression::variable('item.price'),
                        100,
                    ),
                    as: 'item',
                    limit: 1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FilterUsingTheLimitField, $pipeline);
    }
}
