<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $zip expression
 */
class ZipOperatorTest extends PipelineTestCase
{
    public function testFilteringAndPreservingIndexes(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: false,
                pages: Expression::filter(
                    input: Expression::zip([
                        Expression::arrayFieldPath('pages'),
                        Expression::range(0, Expression::size(Expression::arrayFieldPath('pages'))),
                    ]),
                    cond: Expression::let(
                        vars: object(
                            page: Expression::arrayElemAt(Expression::variable('pageWithIndex'), 0),
                        ),
                        in: Expression::gte(Expression::variable('page.reviews'), 1),
                    ),
                    as: 'pageWithIndex',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ZipFilteringAndPreservingIndexes, $pipeline);
    }

    public function testMatrixTransposition(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: false,
                transposed: Expression::zip([
                    Expression::arrayElemAt(Expression::arrayFieldPath('matrix'), 0),
                    Expression::arrayElemAt(Expression::arrayFieldPath('matrix'), 1),
                    Expression::arrayElemAt(Expression::arrayFieldPath('matrix'), 2),
                ]),
            ),
        );

        $this->assertSamePipeline(Pipelines::ZipMatrixTransposition, $pipeline);
    }
}
