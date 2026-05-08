<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Search;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test hasRoot search
 */
class HasRootOperatorTest extends PipelineTestCase
{
    public function testCompoundQuery(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::compound(
                    should: [
                        Search::embeddedDocument(
                            path: 'funding_rounds.investments',
                            operator: Search::wildcard(
                                path: 'funding_rounds.investments.financial_org.name',
                                query: '*Ventures*',
                                allowAnalyzedField: true,
                            ),
                        ),
                        Search::hasRoot(
                            Search::wildcard(
                                path: 'description',
                                query: '*network*',
                                allowAnalyzedField: true,
                            ),
                        ),
                    ],
                ),
                returnScope: object(path: 'funding_rounds'),
                returnStoredSource: true,
            ),
            Stage::limit(5),
        );

        $this->assertSamePipeline(Pipelines::HasRootCompoundQuery, $pipeline);
    }

    public function testMultiLevelQuery(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::hasRoot(
                    Search::text(
                        path: 'name',
                        query: 'Facebook',
                    ),
                ),
                returnScope: ['path' => 'funding_rounds'],
                returnStoredSource: true,
            ),
        );

        $this->assertSamePipeline(Pipelines::HasRootMultiLevelQuery, $pipeline);
    }

    public function testSimpleQuery(): void
    {
        $pipeline = new Pipeline(
            Stage::search(
                Search::hasRoot(
                    Search::range(
                        path: 'founded_year',
                        gte: 2005,
                        lte: 2010,
                    ),
                ),
                returnScope: ['path' => 'products'],
                returnStoredSource: true,
            ),
        );

        $this->assertSamePipeline(Pipelines::HasRootSimpleQuery, $pipeline);
    }
}
