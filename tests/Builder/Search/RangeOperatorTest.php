<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use DateTimeImmutable;
use DateTimeZone;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test range search
 */
class RangeOperatorTest extends PipelineTestCase
{
    public function testDate(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::range(
                    path: 'released',
                    gt: new UTCDateTime(new DateTimeImmutable('2010-01-01T00:00:00.000Z', new DateTimeZone('UTC'))),
                    lt: new UTCDateTime(new DateTimeImmutable('2015-01-01T00:00:00.000Z', new DateTimeZone('UTC'))),
                ),
            ),
            Stage::limit(5),
            Stage::project(
                _id: 0,
                title: 1,
                released: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::RangeDate, $pipeline);
    }

    public function testNumberGteLte(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::range(
                    path: 'runtime',
                    gte: 2,
                    lte: 3,
                ),
            ),
            Stage::limit(5),
            Stage::project(
                _id: 0,
                title: 1,
                runtime: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::RangeNumberGteLte, $pipeline);
    }

    public function testNumberLte(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::range(
                    path: 'runtime',
                    lte: 2,
                ),
            ),
            Stage::limit(5),
            Stage::project(
                _id: 0,
                title: 1,
                runtime: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::RangeNumberLte, $pipeline);
    }

    public function testObjectId(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::range(
                    path: '_id',
                    gte: new ObjectId('573a1396f29313caabce4a9a'),
                    lte: new ObjectId('573a1396f29313caabce4ae7'),
                ),
            ),
            Stage::project(
                _id: 1,
                title: 1,
                released: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::RangeObjectId, $pipeline);
    }

    public function testString(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::range(
                    path: 'title',
                    gt: 'city',
                    lt: 'country',
                ),
            ),
            Stage::limit(5),
            Stage::project(
                _id: 0,
                title: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::RangeString, $pipeline);
    }
}
