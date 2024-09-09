<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $eq query
 */
class EqOperatorTest extends PipelineTestCase
{
    public function testEqualsASpecifiedValue(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                qty: Query::eq(20),
            ),
        );

        $this->assertSamePipeline(Pipelines::EqEqualsASpecifiedValue, $pipeline);
    }

    public function testEqualsAnArrayValue(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                tags: Query::eq(['A', 'B']),
            ),
        );

        $this->assertSamePipeline(Pipelines::EqEqualsAnArrayValue, $pipeline);
    }

    public function testFieldInEmbeddedDocumentEqualsAValue(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                ...['item.name' => Query::eq('ab')],
            ),
        );

        $this->assertSamePipeline(Pipelines::EqFieldInEmbeddedDocumentEqualsAValue, $pipeline);
    }

    public function testRegexMatchBehaviour(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                company: 'MongoDB',
            ),
            Stage::match(
                company: Query::eq('MongoDB'),
            ),
            Stage::match(
                company: new Regex('^MongoDB'),
            ),
            Stage::match(
                company: Query::eq(new Regex('^MongoDB')),
            ),
        );

        $this->assertSamePipeline(Pipelines::EqRegexMatchBehaviour, $pipeline);
    }
}
