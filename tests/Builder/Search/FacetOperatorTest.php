<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use DateTimeImmutable;
use DateTimeZone;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Variable;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test facet search
 */
class FacetOperatorTest extends PipelineTestCase
{
    public function testFacet(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::facet(
                    facets: object(
                        genresFacet: object(
                            type: 'string',
                            path: 'genres',
                        ),
                    ),
                    operator:  Search::near(
                        path: 'released',
                        origin: new UTCDateTime(new DateTimeImmutable('1999-07-01T00:00:00', new DateTimeZone('UTC'))),
                        pivot: 7776000000,
                    ),
                ),
            ),
            Stage::limit(2),
            Stage::facet(
                docs: [
                    Stage::project(
                        title: 1,
                        released: 1,
                    ),
                ],
                meta: [
                    Stage::replaceWith(Variable::variable('SEARCH_META')),
                    Stage::limit(1),
                ],
            ),
            Stage::set(
                meta: Expression::arrayElemAt(Expression::arrayFieldPath('meta'), 0),
            ),
        );

        $this->assertSamePipeline(Pipelines::FacetFacet, $pipeline);
    }
}
