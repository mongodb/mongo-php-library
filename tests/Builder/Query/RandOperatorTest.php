<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $rand query
 */
class RandOperatorTest extends PipelineTestCase
{
    public function testSelectRandomItemsFromACollection(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::expr(
                    Expression::lt(
                        0.5,
                        Expression::rand(),
                    ),
                ),
                district: 3,
            ),
            Stage::project(
                _id: 0,
                name: 1,
                registered: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::RandSelectRandomItemsFromACollection, $pipeline);
    }
}
