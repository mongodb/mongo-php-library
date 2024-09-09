<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use DateTimeImmutable;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\TimeUnit;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $densify stage
 */
class DensifyStageTest extends PipelineTestCase
{
    public function testDensifictionWithPartitions(): void
    {
        $pipeline = new Pipeline(
            Stage::densify(
                field: 'altitude',
                partitionByFields: ['variety'],
                range: object(
                    bounds: 'full',
                    step: 200,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DensifyDensifictionWithPartitions, $pipeline);
    }

    public function testDensifyTimeSeriesData(): void
    {
        $pipeline = new Pipeline(
            Stage::densify(
                field: 'timestamp',
                range: object(
                    step: 1,
                    unit: TimeUnit::Hour,
                    bounds: [
                        new UTCDateTime(new DateTimeImmutable('2021-05-18T00:00:00.000Z')),
                        new UTCDateTime(new DateTimeImmutable('2021-05-18T08:00:00.000Z')),
                    ],
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::DensifyDensifyTimeSeriesData, $pipeline);
    }
}
