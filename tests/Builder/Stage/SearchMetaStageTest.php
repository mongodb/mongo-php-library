<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use DateTimeImmutable;
use DateTimeZone;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $searchMeta stage
 */
class SearchMetaStageTest extends PipelineTestCase
{
    public function testAutocompleteBucketResultsThroughFacetQueries(): void
    {
        $pipeline = new Pipeline(
            Stage::searchMeta(
                Search::facet(
                    facets: object(
                        titleFacet: object(
                            type: 'string',
                            path: 'title',
                        ),
                    ),
                    operator: Search::autocomplete(
                        path: 'title',
                        query: 'Gravity',
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SearchMetaAutocompleteBucketResultsThroughFacetQueries, $pipeline);
    }

    public function testDateFacet(): void
    {
        $pipeline = new Pipeline(
            Stage::searchMeta(
                Search::facet(
                    operator: Search::range(
                        path: 'released',
                        gte: new UTCDateTime(new DateTimeImmutable('2000-01-01', new DateTimeZone('UTC'))),
                        lte: new UTCDateTime(new DateTimeImmutable('2015-01-31', new DateTimeZone('UTC'))),
                    ),
                    facets: object(
                        yearFacet: object(
                            type: 'date',
                            path: 'released',
                            boundaries: [
                                new UTCDateTime(new DateTimeImmutable('2000-01-01', new DateTimeZone('UTC'))),
                                new UTCDateTime(new DateTimeImmutable('2005-01-01', new DateTimeZone('UTC'))),
                                new UTCDateTime(new DateTimeImmutable('2010-01-01', new DateTimeZone('UTC'))),
                                new UTCDateTime(new DateTimeImmutable('2015-01-01', new DateTimeZone('UTC'))),
                            ],
                            default: 'other',
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SearchMetaDateFacet, $pipeline);
    }

    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::searchMeta(
                Search::range(
                    path: 'year',
                    gte: 1998,
                    lt: 1999,
                ),
                count: object(type: 'total'),
            ),
        );

        $this->assertSamePipeline(Pipelines::SearchMetaExample, $pipeline);
    }

    public function testMetadataResults(): void
    {
        $pipeline = new Pipeline(
            Stage::searchMeta(
                Search::facet(
                    operator: Search::range(
                        path: 'released',
                        gte: new UTCDateTime(new DateTimeImmutable('2000-01-01', new DateTimeZone('UTC'))),
                        lte: new UTCDateTime(new DateTimeImmutable('2015-01-31', new DateTimeZone('UTC'))),
                    ),
                    facets: object(
                        directorsFacet: object(
                            type: 'string',
                            path: 'directors',
                            numBuckets: 7,
                        ),
                        yearFacet: object(
                            type: 'number',
                            path: 'year',
                            boundaries: [
                                2000,
                                2005,
                                2010,
                                2015,
                            ],
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SearchMetaMetadataResults, $pipeline);
    }

    public function testYearFacet(): void
    {
        $pipeline = new Pipeline(
            Stage::searchMeta(
                Search::facet(
                    operator: Search::range(
                        path: 'year',
                        gte: 1980,
                        lte: 2000,
                    ),
                    facets: object(
                        yearFacet: object(
                            type: 'number',
                            path: 'year',
                            boundaries: [
                                1980,
                                1990,
                                2000,
                            ],
                            default: 'other',
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SearchMetaYearFacet, $pipeline);
    }
}
