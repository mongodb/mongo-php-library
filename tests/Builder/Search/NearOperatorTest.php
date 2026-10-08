<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use DateTimeImmutable;
use DateTimeZone;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test near search
 */
class NearOperatorTest extends PipelineTestCase
{
    public function testCompound(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::compound(
                    must: Search::text(
                        path: 'property_type',
                        query: 'Apartment',
                    ),
                    should: Search::near(
                        path: 'address.location',
                        origin: object(
                            type: 'Point',
                            coordinates: [
                                114.15027,
                                22.28158,
                            ],
                        ),
                        pivot: 1000,
                    ),
                ),
            ),
            Stage::limit(3),
            Stage::project(
                _id: 0,
                property_type: 1,
                address: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::NearCompound, $pipeline);
    }

    public function testDate(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::near(
                    path: 'released',
                    origin: new UTCDateTime(new DateTimeImmutable('1915-09-13T00:00:00.000+00:00', new DateTimeZone('UTC'))),
                    pivot: 7776000000,
                ),
                index: 'releaseddate',
            ),
            Stage::limit(3),
            Stage::project(
                _id: 0,
                title: 1,
                released: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::NearDate, $pipeline);
    }

    public function testGeoJSONPoint(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::near(
                    path: 'address.location',
                    origin: object(
                        type: 'Point',
                        coordinates: [
                            -8.61308,
                            41.1413,
                        ],
                    ),
                    pivot: 1000,
                ),
            ),
            Stage::limit(3),
            Stage::project(
                _id: 0,
                name: 1,
                address: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::NearGeoJSONPoint, $pipeline);
    }

    public function testNumber(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::near(
                    path: 'runtime',
                    origin: 279,
                    pivot: 2,
                ),
                index: 'runtimes',
            ),
            Stage::limit(7),
            Stage::project(
                _id: 0,
                title: 1,
                runtime: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::NearNumber, $pipeline);
    }
}
