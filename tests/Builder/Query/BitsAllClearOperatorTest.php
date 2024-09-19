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
 * Test $bitsAllClear query
 */
class BitsAllClearOperatorTest extends PipelineTestCase
{
    public function testBinDataBitmask(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                a: Query::bitsAllClear(
                    new Binary(base64_decode('IA==')),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitsAllClearBinDataBitmask, $pipeline);
    }

    public function testBitPositionArray(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                a: Query::bitsAllClear([1, 5]),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitsAllClearBitPositionArray, $pipeline);
    }

    public function testIntegerBitmask(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                a: Query::bitsAllClear(35),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitsAllClearIntegerBitmask, $pipeline);
    }
}
