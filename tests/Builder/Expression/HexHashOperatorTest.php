<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $hexHash expression
 */
class HexHashOperatorTest extends PipelineTestCase
{
    public function testHashAFieldValue(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                filename: 1,
                hexHash: Expression::hexHash(
                    Expression::stringFieldPath('filename'),
                    'sha256',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::HexHashHashAFieldValue, $pipeline);
    }

    public function testNullOrMissingInput(): void
    {
        $pipeline = new Pipeline(
            Stage::documents([
                object(val: null),
                object(),
            ]),
            Stage::project(
                hexHash: Expression::hexHash(
                    Expression::stringFieldPath('val'),
                    'sha256',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::HexHashNullOrMissingInput, $pipeline);
    }
}
