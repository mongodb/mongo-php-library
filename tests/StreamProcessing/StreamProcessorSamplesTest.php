<?php

namespace MongoDB\Tests\StreamProcessing;

use MongoDB\Model\StreamProcessorSamples;
use MongoDB\Tests\TestCase;

class StreamProcessorSamplesTest extends TestCase
{
    public function testGetters(): void
    {
        $samples = new StreamProcessorSamples(42, [['x' => 1], ['x' => 2]]);
        $this->assertSame(42, $samples->getCursorId());
        $this->assertSame([['x' => 1], ['x' => 2]], $samples->getDocuments());
        $this->assertFalse($samples->isExhausted());
    }

    public function testIsExhaustedWhenCursorIdIsZero(): void
    {
        $samples = new StreamProcessorSamples(0, []);
        $this->assertTrue($samples->isExhausted());
    }
}
