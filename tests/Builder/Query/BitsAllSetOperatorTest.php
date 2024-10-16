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
 * Test $bitsAllSet query
 */
class BitsAllSetOperatorTest extends PipelineTestCase
{
    public function testBinDataBitmask(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                a: Query::bitsAllSet(
                    new Binary(base64_decode('MA==')),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitsAllSetBinDataBitmask, $pipeline);
    }

    public function testBitPositionArray(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                a: Query::bitsAllSet([1, 5]),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitsAllSetBitPositionArray, $pipeline);
    }

    public function testIntegerBitmask(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                a: Query::bitsAllSet(50),
            ),
        );

        $this->assertSamePipeline(Pipelines::BitsAllSetIntegerBitmask, $pipeline);
    }
}
