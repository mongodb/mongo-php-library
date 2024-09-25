<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $geoNear stage
 */
class GeoNearStageTest extends PipelineTestCase
{
    public function testMaximumDistance(): void
    {
        $pipeline = new Pipeline(
            Stage::geoNear(
                near: object(
                    type: 'Point',
                    coordinates: [-73.99279, 40.719296],
                ),
                distanceField: 'dist.calculated',
                maxDistance: 2,
                query: Query::query(
                    category: 'Parks',
                ),
                includeLocs: 'dist.location',
                spherical: true,
            ),
        );

        $this->assertSamePipeline(Pipelines::GeoNearMaximumDistance, $pipeline);
    }

    public function testMinimumDistance(): void
    {
        $pipeline = new Pipeline(
            Stage::geoNear(
                near: object(
                    type: 'Point',
                    coordinates: [-73.99279, 40.719296],
                ),
                distanceField: 'dist.calculated',
                minDistance: 2,
                query: Query::query(
                    category: 'Parks',
                ),
                includeLocs: 'dist.location',
                spherical: true,
            ),
        );

        $this->assertSamePipeline(Pipelines::GeoNearMinimumDistance, $pipeline);
    }

    public function testSpecifyWhichGeospatialIndexToUse(): void
    {
        $pipeline = new Pipeline(
            Stage::geoNear(
                near: object(
                    type: 'Point',
                    coordinates: [-73.98142, 40.71782],
                ),
                key: 'location',
                distanceField: 'dist.calculated',
                query: Query::query(
                    category: 'Parks',
                ),
            ),
            Stage::limit(5),
        );

        $this->assertSamePipeline(Pipelines::GeoNearSpecifyWhichGeospatialIndexToUse, $pipeline);
    }

    public function testWithBoundLetOption(): void
    {
        $pipeline = new Pipeline(
            Stage::lookup(
                from: 'places',
                let: object(
                    pt: Expression::stringFieldPath('location'),
                ),
                pipeline: new Pipeline(
                    Stage::geoNear(
                        near: Expression::variable('pt'),
                        distanceField: 'distance',
                    ),
                ),
                as: 'joinedField',
            ),
            Stage::match(
                name: 'Sara D. Roosevelt Park',
            ),
        );

        $this->assertSamePipeline(Pipelines::GeoNearWithBoundLetOption, $pipeline);
    }

    public function testWithTheLetOption(): void
    {
        $pipeline = new Pipeline(
            Stage::geoNear(
                near: Expression::variable('pt'),
                distanceField: 'distance',
                maxDistance: 2,
                query: Query::query(
                    category: 'Parks',
                ),
                includeLocs: 'dist.location',
                spherical: true,
            ),
        );

        $this->assertSamePipeline(Pipelines::GeoNearWithTheLetOption, $pipeline);
    }
}
