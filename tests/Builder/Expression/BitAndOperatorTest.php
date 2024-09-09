<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $bitAnd expression
 */
class BitAndOperatorTest extends PipelineTestCase
{
    public function testBitwiseANDWithALongAndInteger(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                result: Expression::bitAnd(
                    Expression::longFieldPath('a'),
                    new Int64('63'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitAndBitwiseANDWithALongAndInteger, $pipeline);
    }

    public function testBitwiseANDWithTwoIntegers(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                result: Expression::bitAnd(
                    Expression::intFieldPath('a'),
                    Expression::intFieldPath('b'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitAndBitwiseANDWithTwoIntegers, $pipeline);
    }
}
