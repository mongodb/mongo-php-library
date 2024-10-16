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
 * Test $bucket stage
 */
class BucketStageTest extends PipelineTestCase
{
    public function testBucketByYearAndFilterByBucketResults(): void
    {
        $pipeline = new Pipeline(
            Stage::bucket(
                groupBy: Expression::fieldPath('year_born'),
                boundaries: [1840, 1850, 1860, 1870, 1880],
                default: 'Other',
                output: object(
                    count: Accumulator::sum(1),
                    artists: Accumulator::push(
                        object(
                            name: Expression::concat(
                                Expression::stringFieldPath('first_name'),
                                ' ',
                                Expression::stringFieldPath('last_name'),
                            ),
                            year_born: Expression::fieldPath('year_born'),
                        ),
                    ),
                ),
            ),
            Stage::match(
                count: Query::gt(3),
            ),
        );

        $this->assertSamePipeline(Pipelines::BucketBucketByYearAndFilterByBucketResults, $pipeline);
    }

    public function testUseBucketWithFacetToBucketByMultipleFields(): void
    {
        $pipeline = new Pipeline(
            Stage::facet(
                price: new Pipeline(
                    Stage::bucket(
                        groupBy: Expression::numberFieldPath('price'),
                        boundaries: [0, 200, 400],
                        default: 'Other',
                        output: object(
                            count: Accumulator::sum(1),
                            artwork: Accumulator::push(
                                object(
                                    title: Expression::stringFieldPath('title'),
                                    price: Expression::stringFieldPath('price'),
                                ),
                            ),
                            averagePrice: Accumulator::avg(
                                Expression::numberFieldPath('price'),
                            ),
                        ),
                    ),
                ),
                year: new Pipeline(
                    Stage::bucket(
                        groupBy: Expression::stringFieldPath('year'),
                        boundaries: [1890, 1910, 1920, 1940],
                        default: 'Unknown',
                        output: object(
                            count: Accumulator::sum(1),
                            artwork: Accumulator::push(
                                object(
                                    title: Expression::stringFieldPath('title'),
                                    year: Expression::stringFieldPath('year'),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BucketUseBucketWithFacetToBucketByMultipleFields, $pipeline);
    }
}
