<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

use MongoDB\BSON\Binary;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function base64_decode;

/**
 * Test $bitsAnySet query
 */
class BitsAnySetOperatorTest extends PipelineTestCase
{
    public function testBinDataBitmask(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                a: Query::bitsAnySet(
                    new Binary(base64_decode('MA==')),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitsAnySetBinDataBitmask, $pipeline);
    }

    public function testBitPositionArray(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                a: Query::bitsAnySet([1, 5]),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitsAnySetBitPositionArray, $pipeline);
    }

    public function testIntegerBitmask(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                a: Query::bitsAnySet(35),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitsAnySetIntegerBitmask, $pipeline);
    }
}
