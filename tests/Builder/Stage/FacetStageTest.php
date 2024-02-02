<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $facet stage
 */
class FacetStageTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::facet(
                ...[
                    'categorizedByYears(Auto)' => new Pipeline(
                        Stage::bucketAuto(
                            groupBy: Expression::stringFieldPath('year'),
                            buckets: 4,
                        ),
                    ),
                ],
                categorizedByTags: new Pipeline(
                    Stage::unwind(
                        Expression::arrayFieldPath('tags'),
                    ),
                    Stage::sortByCount(
                        Expression::arrayFieldPath('tags'),
                    ),
                ),
                categorizedByPrice: new Pipeline(
                    Stage::match(
                        price: Query::exists(),
                    ),
                    Stage::bucket(
                        groupBy: Expression::numberFieldPath('price'),
                        boundaries: [0, 150, 200, 300, 400],
                        default: 'Other',
                        output: object(
                            count: Accumulator::sum(1),
                            titles: Accumulator::push(
                                Expression::stringFieldPath('title'),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::FacetExample, $pipeline);
    }
}
