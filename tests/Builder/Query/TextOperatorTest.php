<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $text query
 */
class TextOperatorTest extends PipelineTestCase
{
    public function testCaseAndDiacriticInsensitiveSearch(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::text('сы́рники CAFÉS'),
            ),
        );

        $this->assertSamePipeline(Pipelines::TextCaseAndDiacriticInsensitiveSearch, $pipeline);
    }

    public function testDiacriticSensitiveSearch(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::text(
                    search: 'CAFÉ',
                    diacriticSensitive: true,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TextDiacriticSensitiveSearch, $pipeline);
    }

    public function testMatchAnyOfTheSearchTerms(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::text('bake coffee cake'),
            ),
        );

        $this->assertSamePipeline(Pipelines::TextMatchAnyOfTheSearchTerms, $pipeline);
    }

    public function testPerformCaseSensitiveSearch(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::text(
                    search: 'Coffee',
                    caseSensitive: true,
                ),
            ),
            Stage::match(
                Query::text(
                    search: '\"Café Con Leche\"',
                    caseSensitive: true,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TextPerformCaseSensitiveSearch, $pipeline);
    }

    public function testSearchADifferentLanguage(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::text(
                    search: 'leche',
                    language: 'es',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TextSearchADifferentLanguage, $pipeline);
    }

    public function testSearchForASingleWord(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::text('coffee'),
            ),
        );

        $this->assertSamePipeline(Pipelines::TextSearchForASingleWord, $pipeline);
    }

    public function testTextSearchScoreExamples(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::text(
                    search: 'CAFÉ',
                    diacriticSensitive: true,
                ),
            ),
            Stage::project(
                score: Expression::meta('textScore'),
            ),
            Stage::sort(
                score: Sort::TextScore,
            ),
            Stage::limit(5),
        );

        $this->assertSamePipeline(Pipelines::TextTextSearchScoreExamples, $pipeline);
    }
}
