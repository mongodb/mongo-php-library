<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $tsIncrement expression
 */
class TsIncrementOperatorTest extends PipelineTestCase
{
    public function testObtainTheIncrementingOrdinalFromATimestampField(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                saleTimestamp: 1,
                saleIncrement: Expression::tsIncrement(
                    Expression::timestampFieldPath('saleTimestamp'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TsIncrementObtainTheIncrementingOrdinalFromATimestampField, $pipeline);
    }

    public function testUseTsSecondInAChangeStreamCursorToMonitorCollectionChanges(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                Query::expr(
                    Expression::eq(
                        Expression::mod(
                            Expression::tsIncrement(
                                Expression::timestampFieldPath('clusterTime'),
                            ),
                            2,
                        ),
                        0,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::TsIncrementUseTsSecondInAChangeStreamCursorToMonitorCollectionChanges, $pipeline);
    }
}
