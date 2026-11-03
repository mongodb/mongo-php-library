<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

/**
 * Test $bit update
 */
class BitOperatorTest extends UpdateTestCase
{
    public function testBitwiseAND(): void
    {
        $update = Update::bit(expdata: ['and' => 10]);

        $this->assertSameUpdate(Pipelines::BitBitwiseAND, $update);
    }

    public function testBitwiseOR(): void
    {
        $update = Update::bit(expdata: ['or' => 5]);

        $this->assertSameUpdate(Pipelines::BitBitwiseOR, $update);
    }

    public function testBitwiseXOR(): void
    {
        $update = Update::bit(expdata: ['xor' => 5]);

        $this->assertSameUpdate(Pipelines::BitBitwiseXOR, $update);
    }
}
