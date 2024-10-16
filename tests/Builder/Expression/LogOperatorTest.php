<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $log expression
 */
class LogOperatorTest extends PipelineTestCase
{
    public function testExample(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                bitsNeeded: Expression::floor(
                    Expression::add(
                        1,
                        Expression::log(
                            Expression::intFieldPath('int'),
                            2,
                        ),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::LogExample, $pipeline);
    }
}
