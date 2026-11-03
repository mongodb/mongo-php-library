<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $toArray expression
 */
class ToArrayOperatorTest extends PipelineTestCase
{
    public function testConvertBinDataToArray(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                original: Expression::binDataFieldPath('v'),
                asArray: Expression::toArray(
                    Expression::binDataFieldPath('v'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::ToArrayConvertBinDataToArray, $pipeline);
    }

    public function testConvertStringToArray(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                _id: 0,
                numbers: Expression::toArray('[1, 2, 3]'),
                documents: Expression::toArray('[{"a": 1}, {"b": 2}]'),
            ),
        );

        $this->assertSamePipeline(Pipelines::ToArrayConvertStringToArray, $pipeline);
    }
}
