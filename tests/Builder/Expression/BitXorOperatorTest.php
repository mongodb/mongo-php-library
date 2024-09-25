<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $bitXor expression
 */
class BitXorOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                result: Expression::bitXor(
                    Expression::longFieldPath('a'),
                    Expression::longFieldPath('b'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitXorExample, $pipeline);
    }
}
