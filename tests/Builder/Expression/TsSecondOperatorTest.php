<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $tsSecond expression
 */
class TsSecondOperatorTest extends PipelineTestCase
{
    public function testObtainTheNumberOfSecondsFromATimestampField(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                saleTimestamp: 1,
                saleSeconds: Expression::tsSecond(
                    Expression::timestampFieldPath('saleTimestamp'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TsSecondObtainTheNumberOfSecondsFromATimestampField, $pipeline);
    }

    public function testUseTsSecondInAChangeStreamCursorToMonitorCollectionChanges(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                clusterTimeSeconds: Expression::tsSecond(
                    Expression::timestampFieldPath('clusterTime'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TsSecondUseTsSecondInAChangeStreamCursorToMonitorCollectionChanges, $pipeline);
    }
}
