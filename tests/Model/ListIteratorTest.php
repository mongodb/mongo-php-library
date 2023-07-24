<?php

namespace MongoDB\Tests\Model;

use ArrayIterator;
use Generator;
use MongoDB\Model\ListIterator;
use MongoDB\Tests\TestCase;

use function iterator_to_array;

class ListIteratorTest extends TestCase
{
    /** @dataProvider provideTests */
    public function testIteration($source): void
    {
        $iterator = new ListIterator($source);

        $this->assertEquals(['foo', 'bar', 'baz'], iterator_to_array($iterator));
    }

    public static function provideTests(): Generator
    {
        yield 'list' => [new ArrayIterator(['foo', 'bar', 'baz'])];

        yield 'listWithGaps' => [new ArrayIterator([0 => 'foo', 2 => 'bar', 3 => 'baz'])];

        yield 'hash' => [new ArrayIterator(['a' => 'foo', 'b' => 'bar', 'c' => 'baz'])];
    }
}
