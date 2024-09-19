<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $comment query
 */
class CommentOperatorTest extends PipelineTestCase
{
    public function testAttachACommentToAnAggregationExpression(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::comment('Don\'t allow negative inputs.'),
                x: Query::gt(0),
            ),
            Stage::group(
                _id: Expression::mod(
                    Expression::numberFieldPath('x'),
                    2,
                ),
                total: Accumulator::sum(
                    Expression::numberFieldPath('x'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::CommentAttachACommentToAnAggregationExpression, $pipeline);
    }
}
