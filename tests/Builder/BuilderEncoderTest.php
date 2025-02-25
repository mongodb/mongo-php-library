<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder;

use DateTime;
use DateTimeImmutable;
use Generator;
use MongoDB\BSON\Document;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Accumulator;
use MongoDB\Builder\BuilderEncoder;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\FieldPathInterface;
use MongoDB\Builder\Type\Sort;
use MongoDB\Builder\Variable;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\Encoder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function array_merge;
use function MongoDB\object;
use function var_export;

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

    public function testMatchNumericFieldName(): void
    {
        $pipeline = new Pipeline(
            Stage::match(['1' => Query::eq('dave')]),
            Stage::match(['1' => Query::not(Query::eq('dave'))]),
            Stage::match(
                Query::and(
                    Query::query(['2' => Query::gt(3)]),
                    Query::query(['2' => Query::lt(4)]),
                ),
            ),
            Stage::match(
                Query::or(
                    Query::query(['2' => Query::gt(3)]),
                    Query::query(['2' => Query::lt(4)]),
                ),
            ),
            Stage::match(
                Query::nor(
                    Query::query(['2' => Query::gt(3)]),
                    Query::query(['2' => Query::lt(4)]),
                ),
            ),
        );

        $expected = [
            ['$match' => ['1' => ['$eq' => 'dave']]],
            ['$match' => ['1' => ['$not' => ['$eq' => 'dave']]]],
            [
                '$match' => [
                    '$and' => [
                        ['2' => ['$gt' => 3]],
                        ['2' => ['$lt' => 4]],
                    ],
                ],
            ],
            [
                '$match' => [
                    '$or' => [
                        ['2' => ['$gt' => 3]],
                        ['2' => ['$lt' => 4]],
                    ],
                ],
            ],
            [
                '$match' => [
                    '$nor' => [
                        ['2' => ['$gt' => 3]],
                        ['2' => ['$lt' => 4]],
                    ],
                ],
            ],
        ];

        $this->assertSamePipeline($expected, $pipeline);
    }

    /** @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sort/#ascending-descending-sort */
    public function testSort(): void
    {
        $pipeline = new Pipeline(
            Stage::sort(
                age: Sort::Desc,
                posts: Sort::Asc,
            ),
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
     *
     * @param list<int>          $limit
     * @param array<string, int> $expectedLimit
     */
    #[DataProvider('provideExpressionFilterLimit')]
    public function testExpressionFilter(array $limit, array $expectedLimit): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                items: Expression::filter(
                    ...$limit,
                    input: Expression::arrayFieldPath('items'),
                    cond: Expression::gte(Expression::variable('item.price'), 100),
                    as:'item',
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
            ['limit' => 1],
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
                sortBy: object(orderDate: Sort::Asc),
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

    public function testUnionWith(): void
    {
        $pipeline = new Pipeline(
            Stage::unionWith(
                coll: 'orders',
                pipeline: new Pipeline(
                    Stage::match(status: 'A'),
                    Stage::project(
                        item: 1,
                        status: 1,
                    ),
                ),
            ),
        );

        $expected = [
            [
                '$unionWith' => [
                    'coll' => 'orders',
                    'pipeline' => [
                        [
                            '$match' => ['status' => 'A'],
                        ],
                        [
                            '$project' => [
                                'item' => 1,
                                'status' => 1,
                            ],
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
                    then: Variable::prune(),
                    else: Variable::descend(),
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

    public function testDateTimeEncoding(): void
    {
        $dateTimeImmutable = new DateTimeImmutable();
        $dateTime = DateTime::createFromImmutable($dateTimeImmutable);
        $utcDateTime = new UTCDateTime($dateTime);

        $pipeline = new Pipeline(
            Stage::match(
                utc: $utcDateTime,
                mutable: $dateTime,
                immutable: $dateTimeImmutable,
            ),
            Stage::addFields(
                utc: Expression::dateToString($utcDateTime),
                mutable: Expression::dateToString($dateTime),
                immutable: Expression::dateToString($dateTimeImmutable),
            ),
        );

        $expected = [
            [
                '$match' => [
                    'utc' => $utcDateTime,
                    'mutable' => $utcDateTime,
                    'immutable' => $utcDateTime,
                ],
            ],
            [
                '$addFields' => [
                    'utc' => ['$dateToString' => ['date' => $utcDateTime]],
                    'mutable' => ['$dateToString' => ['date' => $utcDateTime]],
                    'immutable' => ['$dateToString' => ['date' => $utcDateTime]],
                ],
            ],
        ];

        $this->assertSamePipeline($expected, $pipeline);
    }

    public function testCustomEncoder(): void
    {
        $customEncoders = [
            FieldPathInterface::class => new class implements Encoder {
                use EncodeIfSupported;

                public function canEncode(mixed $value): bool
                {
                    return $value instanceof FieldPathInterface;
                }

                public function encode(mixed $value)
                {
                    return '$prefix.' . $value->name;
                }
            },
        ];
        $codec = new BuilderEncoder($customEncoders);

        $pipeline = new Pipeline(
            Stage::project(
                threeFavorites: Expression::slice(
                    Expression::arrayFieldPath('items'),
                    n: 3,
                ),
            ),
        );

        $expected = [
            [
                '$project' => [
                    'threeFavorites' => [
                        '$slice' => ['$prefix.items', 3],
                    ],
                ],
            ],
        ];

        $this->assertSamePipeline($expected, $pipeline, $codec);
    }

    /** @param list<array<string, mixed>> $expected */
    private static function assertSamePipeline(array $expected, Pipeline $pipeline, $codec = new BuilderEncoder()): void
    {
        $actual = $codec->encode($pipeline);

        // Normalize with BSON round-trip
        // BSON Documents doesn't support top-level arrays.
        $actual = Document::fromPHP(['root' => $actual])->toCanonicalExtendedJSON();
        $expected = Document::fromPHP(['root' => $expected])->toCanonicalExtendedJSON();

        self::assertJsonStringEqualsJsonString($expected, $actual, var_export($actual, true));
    }
}
