<?php

namespace MongoDB\Tests\Builder;

use Generator;
use MongoDB\Builder\Accumulator;
use MongoDB\Builder\BuilderEncoder;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Projection;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\TestCase;

use function array_is_list;
use function array_merge;
use function array_walk;
use function is_array;
use function MongoDB\object;
use function var_export;

/**
 * @todo This annotation is not enough as this PHP file needs to use named arguments, that can't compile on PHP 7.4
 * @requires PHP 8.1
 */
class BuilderEncoderTest extends TestCase
{
    /** @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/#equality-match */
    public function testPipeline(): void
    {
        $pipeline = new Pipeline(
            Stage::match(author: 'dave'),
            Stage::limit(1),
        );

        $expected = [
            ['$match' => ['author' => 'dave']],
            ['$limit' => 1],
        ];

        $this->assertSamePipeline($expected, $pipeline);
    }

    /** @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sort/#ascending-descending-sort */
    public function testSort(): void
    {
        $pipeline = new Pipeline(
            Stage::sort(object(age: -1, posts: 1)),
        );

        $expected = [
            ['$sort' => ['age' => -1, 'posts' => 1]],
        ];

        $this->assertSamePipeline($expected, $pipeline);
    }

    /** @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/#perform-a-count */
    public function testPerformCount(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::or(
                    Query::query(score: [Query::gt(70), Query::lt(90)]),
                    Query::query(views: Query::gte(1000)),
                ),
            ),
            Stage::group(
                _id: null,
                count: Accumulator::sum(1),
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
     * @dataProvider provideExpressionFilterLimit
     */
    public function testExpressionFilter(array $limit, array $expectedLimit): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                items: Projection::filter(
                    Expression::arrayFieldPath('items'),
                    Expression::gte(Expression::variable('item.price'), 100),
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

    public static function provideExpressionFilterLimit(): Generator
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

    /** @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/slice/#example */
    public function testSlice(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                name: 1,
                threeFavorites: Expression::slice(
                    Expression::arrayFieldPath('items'),
                    n: 3,
                ),
            ),
        );

        $expected = [
            [
                '$project' => [
                    'name' => 1,
                    'threeFavorites' => [
                        '$slice' => ['$items', 3],
                    ],
                ],
            ],
        ];

        $this->assertSamePipeline($expected, $pipeline);
    }

    /** @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#use-documents-window-to-obtain-cumulative-and-maximum-quantity-for-each-year */
    public function testSetWindowFields(): void
    {
        $pipeline = new Pipeline(
            Stage::setWindowFields(
                partitionBy: Expression::year(Expression::dateFieldPath('orderDate')),
                sortBy: object(orderDate: 1),
                output: object(
                    cumulativeQuantityForYear: Accumulator::outputWindow(
                        Accumulator::sum(Expression::intFieldPath('quantity')),
                        documents: ['unbounded', 'current'],
                    ),
                    maximumQuantityForYear: Accumulator::outputWindow(
                        Accumulator::max(Expression::intFieldPath('quantity')),
                        documents: ['unbounded', 'unbounded'],
                    ),
                ),
            ),
        );

        $expected = [
            [
                '$setWindowFields' => [
                    // "date" key is optional for $year, but we always add it for consistency
                    'partitionBy' => ['$year' => ['date' => '$orderDate']],
                    'sortBy' => ['orderDate' => 1],
                    'output' => [
                        'cumulativeQuantityForYear' => [
                            '$sum' => '$quantity',
                            'window' => ['documents' => ['unbounded', 'current']],
                        ],
                        'maximumQuantityForYear' => [
                            '$max' => '$quantity',
                            'window' => ['documents' => ['unbounded', 'unbounded']],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertSamePipeline($expected, $pipeline);
    }

    /** @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/redact/ */
    public function testRedactStage(): void
    {
        $pipeline = new Pipeline(
            Stage::match(status: 'A'),
            Stage::redact(
                Expression::cond(
                    if: Expression::eq(Expression::fieldPath('level'), 5),
                    then: '$$PRUNE',
                    else: '$$DESCEND',
                ),
            ),
        );
        $expected = [
            [
                '$match' => ['status' => 'A'],
            ],
            [
                '$redact' => [
                    '$cond' => [
                        'if' => ['$eq' => ['$level', 5]],
                        'then' => '$$PRUNE',
                        'else' => '$$DESCEND',
                    ],
                ],
            ],
        ];

        $this->assertSamePipeline($expected, $pipeline);
    }

    private static function assertSamePipeline(array $expected, Pipeline $pipeline): void
    {
        $codec = new BuilderEncoder();
        $actual = $codec->encode($pipeline);

        self::objectify($expected);

        self::assertEquals($expected, $actual, var_export($actual, true));
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
