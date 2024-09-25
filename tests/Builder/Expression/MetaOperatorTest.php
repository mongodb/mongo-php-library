<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $meta expression
 */
class MetaOperatorTest extends PipelineTestCase
{
    public function testIndexKey(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                type: 'apparel',
            ),
            Stage::addFields(
                idxKey: Expression::meta('indexKey'),
            ),
        );

        $this->assertSamePipeline(Pipelines::MetaIndexKey, $pipeline);
    }

    public function testTextScore(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::text(
                    search: 'cake',
                ),
            ),
            Stage::group(
                _id: Expression::meta('textScore'),
                count: Accumulator::sum(1),
            ),
        );

        $this->assertSamePipeline(Pipelines::MetaTextScore, $pipeline);
    }
}
