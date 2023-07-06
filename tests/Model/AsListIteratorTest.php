<?php

namespace MongoDB\Tests\Model;

use ArrayIterator;
use Generator;
use MongoDB\Model\AsListIterator;
use MongoDB\Tests\TestCase;

use function iterator_to_array;

class AsListIteratorTest extends TestCase
{
    /** @dataProvider provideTests */
    public function testIteration($source): void
    {
        $iterator = new AsListIterator($source);

        $this->assertEquals(['foo', 'bar', 'baz'], iterator_to_array($iterator));
    }

    public static function provideTests(): Generator
    {
        yield 'list' => [new ArrayIterator(['foo', 'bar', 'baz'])];

        yield 'listWithGaps' => [new ArrayIterator([0 => 'foo', 2 => 'bar', 3 => 'baz'])];

        yield 'hash' => [new ArrayIterator(['a' => 'foo', 'b' => 'bar', 'c' => 'baz'])];
    }
}
