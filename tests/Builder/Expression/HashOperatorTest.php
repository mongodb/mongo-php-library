<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $hash expression
 */
class HashOperatorTest extends PipelineTestCase
{
    public function testHashAFieldValue(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                filename: 1,
                hash: Expression::hash(
                    Expression::stringFieldPath('filename'),
                    'sha256',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::HashHashAFieldValue, $pipeline);
    }

    public function testHashALiteralString(): void
    {
        $pipeline = new Pipeline(
            Stage::documents([
                object(val: 'hello'),
            ]),
            Stage::project(
                _id: 0,
                hash: Expression::hash(
                    Expression::stringFieldPath('val'),
                    'xxh64',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::HashHashALiteralString, $pipeline);
    }

    public function testHashBinData(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                hash: Expression::hash(
                    Expression::binDataFieldPath('data'),
                    'sha256',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::HashHashBinData, $pipeline);
    }

    public function testNullOrMissingInput(): void
    {
        $pipeline = new Pipeline(
            Stage::documents([
                object(val: null),
                object(),
            ]),
            Stage::project(
                hash: Expression::hash(
                    Expression::stringFieldPath('val'),
                    'sha256',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::HashNullOrMissingInput, $pipeline);
    }
}
