<?php

namespace MongoDB\Tests\Model;

use ArrayIterator;
use MongoDB\Model\CallbackIterator;
use MongoDB\Tests\TestCase;

use function array_keys;
use function iterator_to_array;
use function strrev;

class CallbackIteratorTest extends TestCase
{
    public function testArrayIteration(): void
    {
        $expectedKey = 0;

        $original = [1, 2, 3];

        $callbackIterator = new CallbackIterator(
            new ArrayIterator($original),
            function ($value, $key) use (&$expectedKey) {
                $this->assertSame($expectedKey, $key);
                $expectedKey++;

                return $value * 2;
            }
        );

        $this->assertSame([2, 4, 6], iterator_to_array($callbackIterator));
    }

    public function testHashIteration(): void
    {
        $expectedKey = 0;

        $original = ['a' => 1, 'b' => 2, 'c' => 3];
        $expectedKeys = array_keys($original);

        $callbackIterator = new CallbackIterator(
            new ArrayIterator($original),
            function ($value, $key) use (&$expectedKey, $expectedKeys) {
                $this->assertSame($expectedKeys[$expectedKey], $key);
                $expectedKey++;

                return $value * 2;
            }
        );

        $this->assertSame(['a' => 2, 'b' => 4, 'c' => 6], iterator_to_array($callbackIterator));
    }

    public function testWithCallable(): void
    {
        $original = ['foo', 'bar', 'baz'];

        $callbackIterator = new CallbackIterator(
            new ArrayIterator($original),
            [self::class, 'reverseValue']
        );

        $this->assertSame(['oof', 'rab', 'zab'], iterator_to_array($callbackIterator));
    }

    public static function reverseValue($value, $key)
    {
        return strrev($value);
    }
}
