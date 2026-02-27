<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use DateTime;
use DateTimeZone;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Variable;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $search stage
 */
class SearchStageTest extends PipelineTestCase
{
    public function testCountResults(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::near(
                    path: 'released',
                    origin: new UTCDateTime(new DateTime('2011-09-01T00:00:00.000+00:00', new DateTimeZone('UTC'))),
                    pivot: 7776000000,
                ),
                count: object(type: 'total'),
            ),
            Stage::project(
                meta: Variable::searchMeta(),
                title: 1,
                released: 1,
            ),
            Stage::limit(2),
        );

        $this->assertSamePipeline(Pipelines::SearchCountResults, $pipeline);
    }

    public function testDateSearchAndSort(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::range(
                    path: 'released',
                    gt: new UTCDateTime(new DateTime('2010-01-01', new DateTimeZone('UTC'))),
                    lt: new UTCDateTime(new DateTime('2015-01-01', new DateTimeZone('UTC'))),
                ),
                sort: object(
                    released: -1,
                ),
            ),
            Stage::limit(5),
            Stage::project(
                _id: 0,
                title: 1,
                released: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::SearchDateSearchAndSort, $pipeline);
    }

    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::near(
                    path: 'released',
                    origin: new UTCDateTime(new DateTime('2011-09-01T00:00:00.000+00:00', new DateTimeZone('UTC'))),
                    pivot: 7776000000,
                ),
            ),
            Stage::project(_id: 0, title: 1, released: 1),
            Stage::limit(5),
            Stage::facet(
                docs: [],
                meta: new Pipeline(
                    Stage::replaceWith(Expression::variable('SEARCH_META')),
                    Stage::limit(1),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SearchExample, $pipeline);
    }

    public function testNumberSearchAndSort(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::range(
                    path: 'awards.wins',
                    gt: 3,
                ),
                sort: ['awards.wins' => -1],
            ),
            Stage::limit(5),
            Stage::project(...[
                '_id' => 0,
                'title' => 1,
                'awards.wins' => 1,
            ]),
        );

        $this->assertSamePipeline(Pipelines::SearchNumberSearchAndSort, $pipeline);
    }

    public function testPaginateResultsAfterAToken(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::text(
                    path: 'title',
                    query: 'war',
                ),
                searchAfter: 'CMtJGgYQuq+ngwgaCSkAjBYH7AAAAA==',
                sort: object(
                    score: ['$meta' => 'searchScore'],
                    released: 1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SearchPaginateResultsAfterAToken, $pipeline);
    }

    public function testPaginateResultsBeforeAToken(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::text(
                    path: 'title',
                    query: 'war',
                ),
                searchBefore: 'CJ6kARoGELqvp4MIGgkpACDA3U8BAAA=',
                sort: object(
                    score: ['$meta' => 'searchScore'],
                    released: 1,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::SearchPaginateResultsBeforeAToken, $pipeline);
    }

    public function testReturnStoredSourceFields(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::text(
                    path: 'title',
                    query: 'baseball',
                ),
                returnStoredSource: true,
            ),
            Stage::match(
                Query::or(
                    Query::query(...['imdb.rating' => Query::gt(8.2)]),
                    Query::query(...['imdb.votes' => Query::gte(4500)]),
                ),
            ),
            Stage::lookup(
                as: 'document',
                from: 'movies',
                localField: '_id',
                foreignField: '_id',
            ),
        );

        $this->assertSamePipeline(Pipelines::SearchReturnStoredSourceFields, $pipeline);
    }

    public function testSortByScore(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::text(
                    path: 'title',
                    query: 'story',
                ),
                sort: object(
                    score: [
                        '$meta' => 'searchScore',
                        'order' => 1,
                    ],
                ),
            ),
            Stage::limit(5),
            Stage::project(
                _id: 0,
                title: 1,
                score: ['$meta' => 'searchScore'],
            ),
        );

        $this->assertSamePipeline(Pipelines::SearchSortByScore, $pipeline);
    }

    public function testTrackSearchTerms(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::text(
                    query: 'summer',
                    path: 'title',
                ),
                tracking: object(searchTerms: 'summer'),
            ),
            Stage::limit(5),
            Stage::project(
                _id: 0,
                title: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::SearchTrackSearchTerms, $pipeline);
    }
}
