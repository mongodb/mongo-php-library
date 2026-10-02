<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Encoder;

use MongoDB\Builder\Accumulator;
use MongoDB\Builder\BuilderEncoder;
use PHPUnit\Framework\TestCase;
use stdClass;

class OutputWindowEncoderTest extends TestCase
{
    public function testSharedOperatorIsNotModified(): void
    {
        $operator = (object) ['$sum' => '$x'];
        $encoder = new BuilderEncoder();

        $first = $encoder->encode(Accumulator::outputWindow($operator, documents: [-1, 0]));
        $second = $encoder->encode(Accumulator::outputWindow($operator, documents: [0, 1]));

        $this->assertEquals((object) ['$sum' => '$x', 'window' => (object) ['documents' => [-1, 0]]], $first);
        $this->assertEquals((object) ['$sum' => '$x', 'window' => (object) ['documents' => [0, 1]]], $second);
        $this->assertEquals((object) ['$sum' => '$x'], $operator);
        $this->assertInstanceOf(stdClass::class, $first);
    }
}
