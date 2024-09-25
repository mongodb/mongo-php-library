<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $geoWithin query
 */
class GeoWithinOperatorTest extends PipelineTestCase
{
    public function testWithinABigPolygon(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                loc: Query::geoWithin(
                    Query::geometry(
                        type: 'Polygon',
                        coordinates: [[[-100, 60], [-100, 0], [-100, -60], [100, -60], [100, 60], [-100, 60]]],
                        crs: object(
                            type: 'name',
                            properties: object(
                                name: 'urn:x-mongodb:crs:strictwinding:EPSG:4326',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::GeoWithinWithinABigPolygon, $pipeline);
    }

    public function testWithinAPolygon(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                loc: Query::geoWithin(
                    Query::geometry(
                        type: 'Polygon',
                        coordinates: [[[0, 0], [3, 6], [6, 1], [0, 0]]],
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::GeoWithinWithinAPolygon, $pipeline);
    }
}
