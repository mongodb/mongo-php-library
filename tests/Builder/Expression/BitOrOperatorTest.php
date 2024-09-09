<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $bitOr expression
 */
class BitOrOperatorTest extends PipelineTestCase
{
    public function testBitwiseORWithALongAndInteger(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                result: Expression::bitOr(
                    Expression::longFieldPath('a'),
                    new Int64('63'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitOrBitwiseORWithALongAndInteger, $pipeline);
    }

    public function testBitwiseORWithTwoIntegers(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                result: Expression::bitOr(
                    Expression::intFieldPath('a'),
                    Expression::intFieldPath('b'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitOrBitwiseORWithTwoIntegers, $pipeline);
    }
}
