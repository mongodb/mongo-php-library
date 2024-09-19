<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $percentile expression
 */
class PercentileOperatorTest extends PipelineTestCase
{
    public function testUsePercentileInAProjectStage(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                studentId: 1,
                testPercentiles: Expression::percentile(
                    input: [
                        Expression::intFieldPath('test01'),
                        Expression::longFieldPath('test02'),
                        Expression::numberFieldPath('test03'),
                    ],
                    p: [0.5, 0.95],
                    method: 'approximate',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::PercentileUsePercentileInAProjectStage, $pipeline);
    }
}
