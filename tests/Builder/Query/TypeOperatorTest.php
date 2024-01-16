<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $type query
 */
class TypeOperatorTest extends PipelineTestCase
{
    public function testQueryingByArrayType(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                zipCode: Query::type('array'),
            ),
        );

        $this->assertSamePipeline(Pipelines::TypeQueryingByArrayType, $pipeline);
    }

    public function testQueryingByDataType(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                zipCode: Query::type(2),
            ),
            Stage::match(
                zipCode: Query::type('string'),
            ),
            Stage::match(
                zipCode: Query::type(1),
            ),
            Stage::match(
                zipCode: Query::type('double'),
            ),
            Stage::match(
                zipCode: Query::type('number'),
            ),
        );

        $this->assertSamePipeline(Pipelines::TypeQueryingByDataType, $pipeline);
    }

    public function testQueryingByMinKeyAndMaxKey(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                zipCode: Query::type('minKey'),
            ),
            Stage::match(
                zipCode: Query::type('maxKey'),
            ),
        );

        $this->assertSamePipeline(Pipelines::TypeQueryingByMinKeyAndMaxKey, $pipeline);
    }

    public function testQueryingByMultipleDataType(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                zipCode: Query::type(2, 1),
            ),
            Stage::match(
                zipCode: Query::type('string', 'double'),
            ),
        );

        $this->assertSamePipeline(Pipelines::TypeQueryingByMultipleDataType, $pipeline);
    }
}
