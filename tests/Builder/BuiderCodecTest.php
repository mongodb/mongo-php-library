<?php

namespace MongoDB\Tests\Builder;

use MongoDB\Builder\Aggregation;
use MongoDB\Builder\BuilderCodec;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\TestCase;

class BuiderCodecTest extends TestCase
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

    private static function assertSamePipeline(array $expected, Pipeline $actual): void
    {
        $codec = new BuilderCodec();

        self::assertEquals($expected, $codec->encode($actual));
    }
}
