<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $bitNot expression
 */
class BitNotOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                result: Expression::bitNot(
                    Expression::longFieldPath('a'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitNotExample, $pipeline);
    }
}
