<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $median expression
 */
class MedianOperatorTest extends PipelineTestCase
{
    public function testUseMedianInAProjectStage(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                studentId: 1,
                testMedians: Expression::median(
                    input: [
                        Expression::numberFieldPath('test01'),
                        Expression::numberFieldPath('test02'),
                        Expression::numberFieldPath('test03'),
                    ],
                    method: 'approximate',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MedianUseMedianInAProjectStage, $pipeline);
    }
}
