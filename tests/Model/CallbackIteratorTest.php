<?php

namespace MongoDB\Tests\Model;

use ArrayIterator;
use Generator;
use Iterator;
use IteratorAggregate;
use MongoDB\Model\CallbackIterator;
use MongoDB\Tests\TestCase;

use function iterator_to_array;

class CallbackIteratorTest extends TestCase
{
    /** @dataProvider provideTests */
    public function testIteration($expected, $source, $callback): void
    {
        $callbackIterator = new CallbackIterator($source, $callback);

        $this->assertEquals($expected, iterator_to_array($callbackIterator));
    }

    public static function provideTests(): Generator
    {
        $listIterator = new ArrayIterator([1, 2, 3]);
        $hashIterator = new ArrayIterator(['a' => 1, 'b' => 2, 'c' => 3]);

        $iteratorAggregate = new class ($listIterator) implements IteratorAggregate
        {
            private Iterator $iterator;

            public function __construct(Iterator $iterator)
            {
                $this->iterator = $iterator;
            }

            public function getIterator(): Iterator
            {
                return $this->iterator;
            }
        };

        yield 'List with closure' => [
            'expected' => [2, 4, 6],
            'source' => $listIterator,
            'callback' => function ($value, $key) use ($listIterator) {
                self::assertSame($listIterator->key(), $key);

                return $value * 2;
            },
        ];

        yield 'List with callable' => [
            'expected' => [2, 4, 6],
            'source' => $listIterator,
            'callback' => [self::class, 'doubleValue'],
        ];

        yield 'Hash with closure' => [
            'expected' => ['a' => 2, 'b' => 4, 'c' => 6],
            'source' => $hashIterator,
            'callback' => function ($value, $key) use ($hashIterator) {
                self::assertSame($hashIterator->key(), $key);

                return $value * 2;
            },
        ];

        yield 'IteratorAggregate with closure' => [
            'expected' => [2, 4, 6],
            'source' => $iteratorAggregate,
            'callback' => function ($value, $key) use ($listIterator) {
                self::assertSame($listIterator->key(), $key);

                return $value * 2;
            },
        ];
    }

    public static function doubleValue($value, $key)
    {
        return $value * 2;
    }
}
