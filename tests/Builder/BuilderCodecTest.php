<?php

namespace MongoDB\Tests\Builder;

use Generator;
use MongoDB\Builder\Aggregation;
use MongoDB\Builder\BuilderEncoder;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\TestCase;

use function array_merge;

class BuilderCodecTest extends TestCase
{
    public function testPipeline(): void
    {
        $pipeline = new Pipeline(
            Stage::match(Aggregation::eq('$foo', 1)),
            Stage::match(Aggregation::ne('$foo', 2)),
            Stage::limit(1),
        );

        $expected = [
            (object) ['$match' => (object) ['$eq' => ['$foo', 1]]],
            (object) ['$match' => (object) ['$ne' => ['$foo', 2]]],
            (object) ['$limit' => 1],
        ];

        $this->assertSamePipeline($expected, $pipeline);
    }

    /** @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/#perform-a-count */
    public function testPerformCount(): void
    {
        $pipeline = new Pipeline(
            Stage::match(Query::or(
                ['score' => [Query::gt(70), Query::lt(90)]],
                ['views' => Query::gte(1000)],
            )),
            Stage::group(null, ['count' => Aggregation::sum(1)]),
        );

        $expected = [
            (object) [
                '$match' => (object) [
                    '$or' => [
                        (object) ['score' => (object) ['$gt' => 70, '$lt' => 90]],
                        (object) ['views' => (object) ['$gte' => 1000]],
                    ],
                ],
            ],
            (object) [
                '$group' => (object) [
                    '_id' => null,
                    'count' => (object) ['$sum' => 1],
                ],
            ],
        ];

        $this->assertSamePipeline($expected, $pipeline);
    }

    /**
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/#examples
     * @dataProvider provideAggregationFilterLimit
     */
    public function testAggregationFilter($limit, $expectedLimit): void
    {
        $pipeline = new Pipeline(
            Stage::project([
                'items' => Aggregation::filter(
                    input: Expression::arrayFieldPath('items'),
                    cond: Aggregation::gte(Expression::variable('item.price'), 100),
                    as: 'item',
                    limit: $limit,
                ),
            ]),
        );

        $expected = [
            (object) [
                '$project' => (object) [
                    'items' => (object) [
                        '$filter' => (object) array_merge([
                            'input' => '$items',
                            'as' => 'item',
                            'cond' => (object) ['$gte' => ['$$item.price', 100]],
                        ], $expectedLimit),
                    ],
                ],
            ],
        ];

        $this->assertSamePipeline($expected, $pipeline);
    }

    public static function provideAggregationFilterLimit(): Generator
    {
        yield 'unspecified limit' => [
            null,
            [],
        ];

        yield 'int limit' => [
            1,
            ['limit' => 1],
        ];
    }

    private static function assertSamePipeline(array $expected, Pipeline $pipeline): void
    {
        $codec = new BuilderEncoder();
        $actual = $codec->encode($pipeline);

        // @todo walk in array to cast associative array to an object
        self::assertEquals($expected, $actual);
    }
}
