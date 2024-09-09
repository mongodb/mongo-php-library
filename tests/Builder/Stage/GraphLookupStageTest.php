<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $graphLookup stage
 */
class GraphLookupStageTest extends PipelineTestCase
{
    public function testAcrossMultipleCollections(): void
    {
        $pipeline = new Pipeline(
            Stage::graphLookup(
                from: 'airports',
                startWith: Expression::stringFieldPath('nearestAirport'),
                connectFromField: 'connects',
                connectToField: 'airport',
                maxDepth: 2,
                depthField: 'numConnections',
                as: 'destinations',
            ),
        );

        $this->assertSamePipeline(Pipelines::GraphLookupAcrossMultipleCollections, $pipeline);
    }

    public function testWithAQueryFilter(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                name: 'Tanya Jordan',
            ),
            Stage::graphLookup(
                from: 'people',
                startWith: Expression::stringFieldPath('friends'),
                connectFromField: 'friends',
                connectToField: 'name',
                as: 'golfers',
                restrictSearchWithMatch: Query::query(
                    hobbies: 'golf',
                ),
            ),
            Stage::project(
                ...[
                    'connections who play golf' => Expression::stringFieldPath('golfers.name'),
                ],
                name: 1,
                friends: 1,
            ),
        );

        $this->assertSamePipeline(Pipelines::GraphLookupWithAQueryFilter, $pipeline);
    }

    public function testWithinASingleCollection(): void
    {
        $pipeline = new Pipeline(
            Stage::graphLookup(
                from: 'employees',
                startWith: Expression::stringFieldPath('reportsTo'),
                connectFromField: 'reportsTo',
                connectToField: 'name',
                as: 'reportingHierarchy',
            ),
        );

        $this->assertSamePipeline(Pipelines::GraphLookupWithinASingleCollection, $pipeline);
    }
}
