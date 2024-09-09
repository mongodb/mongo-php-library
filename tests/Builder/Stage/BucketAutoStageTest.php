<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $bucketAuto stage
 */
class BucketAutoStageTest extends PipelineTestCase
{
    public function testSingleFacetAggregation(): void
    {
        $pipeline = new Pipeline(
            Stage::bucketAuto(
                groupBy: Expression::fieldPath('price'),
                buckets: 4,
            ),
        );

        $this->assertSamePipeline(Pipelines::BucketAutoSingleFacetAggregation, $pipeline);
    }
}
