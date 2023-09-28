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

use function array_is_list;
use function array_merge;
use function array_walk;
use function is_array;

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
            ['$match' => ['$eq' => ['$foo', 1]]],
            ['$match' => ['$ne' => ['$foo', 2]]],
            ['$limit' => 1],
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
            [
                '$match' => [
                    '$or' => [
                        ['score' => ['$gt' => 70, '$lt' => 90]],
                        ['views' => ['$gte' => 1000]],
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => null,
                    'count' => ['$sum' => 1],
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
            [
                '$project' => [
                    'items' => [
                        '$filter' => array_merge([
                            'input' => '$items',
                            'as' => 'item',
                            'cond' => ['$gte' => ['$$item.price', 100]],
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

        self::objectify($expected);

        self::assertEquals($expected, $actual);
    }

    /**
     * Recursively convert associative arrays to objects.
     */
    private static function objectify(array &$array): void
    {
        array_walk($array, function (&$value): void {
            if (is_array($value)) {
                self::objectify($value);

                if (! array_is_list($value)) {
                    $value = (object) $value;
                }
            }
        });
    }
}
