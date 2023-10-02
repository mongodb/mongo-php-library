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

/**
 * @todo This annotation is not enough as this PHP file needs to use named arguments, that can't compile on PHP 7.4
 * @requires PHP 8.0
 */
class BuilderCodecTest extends TestCase
{
    /** @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/#equality-match */
    public function testPipeline(): void
    {
        $pipeline = new Pipeline(
            // @todo array is accepted by the stage class, but we expect an object. The driver accepts both.
            Stage::match((object) ['author' => 'dave']),
            Stage::limit(1),
        );

        $expected = [
            ['$match' => ['author' => 'dave']],
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
            Stage::group(
                _id: null,
                count: Aggregation::sum(1),
            ),
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
    public function testAggregationFilter(array $limit, array $expectedLimit): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                items: Aggregation::filter(
                    Expression::arrayFieldPath('items'),
                    Aggregation::gte(Expression::variable('item.price'), 100),
                    'item',
                    ...$limit,
                ),
            ),
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
            [],
            [],
        ];

        yield 'int limit' => [
            [1],
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
