<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $split expression
 */
class SplitOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                city_state: Expression::split(
                    Expression::stringFieldPath('city'),
                    ', ',
                ),
                qty: 1,
            ),
            Stage::unwind(
                Expression::arrayFieldPath('city_state'),
            ),
            Stage::match(
                city_state: new Regex('[A-Z]{2}'),
            ),
            Stage::group(
                _id: object(
                    state: Expression::stringFieldPath('city_state'),
                ),
                total_qty: Accumulator::sum(
                    Expression::intFieldPath('qty'),
                ),
            ),
            Stage::sort(
                total_qty: Sort::Desc,
            ),
        );

        $this->assertSamePipeline(Pipelines::SplitExample, $pipeline);
    }
}
