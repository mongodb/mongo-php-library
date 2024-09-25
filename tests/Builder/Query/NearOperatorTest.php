<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $near query
 */
class NearOperatorTest extends PipelineTestCase
{
    public function testQueryOnGeoJSONData(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                location: Query::near(
                    Query::geometry(
                        type: 'Point',
                        coordinates: [-73.9667, 40.78],
                    ),
                    minDistance: 1000,
                    maxDistance: 5000,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::NearQueryOnGeoJSONData, $pipeline);
    }
}
