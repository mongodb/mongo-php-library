<?php

namespace MongoDB\Tests\Model;

use Generator;
use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\LazyBSONArray;
use MongoDB\Model\LazyBSONDocument;
use MongoDB\Tests\TestCase;

use function iterator_to_array;
use function json_encode;

class LazyBSONArrayTest extends TestCase
{
    private const ARRAY = [
        'bar',
        ['bar' => 'baz'],
        [0, 1, 2],
    ];

    public static function provideTestArray(): Generator
    {
        yield 'array' => [new LazyBSONArray(self::ARRAY)];

        yield 'packedArray' => [new LazyBSONArray(PackedArray::fromPHP(self::ARRAY))];
    }

    public function testConstructWithoutArgument(): void
    {
        $instance = new LazyBSONArray();
        $this->assertSame([], iterator_to_array($instance));
    }

    public function testConstructWithWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new LazyBSONArray('foo');
    }

    public function testClone(): void
    {
        $original = new LazyBSONArray();
        $original[0] = (object) ['foo' => 'bar'];

        $clone = clone $original;
        $clone[0]->foo = 'baz';

        self::assertSame('bar', $original[0]->foo);
    }

    /** @dataProvider provideTestArray */
    public function testOffsetGet(LazyBSONArray $array): void
    {
        $this->assertSame('bar', $array[0]);
    }

    /** @dataProvider provideTestArray */
    public function testOffsetGetAfterUnset(LazyBSONArray $array): void
    {
        $this->assertSame('bar', $array[0]);
        unset($array[0]);

        $this->expectWarning();
        $this->expectWarningMessage('Undefined offset: 0');
        $array[0];
    }

    /** @dataProvider provideTestArray */
    public function testOffsetGetForMissingOffset(LazyBSONArray $array): void
    {
        $this->expectWarning();
        $this->expectWarningMessage('Undefined offset: 4');
        $array[4];
    }

    /** @dataProvider provideTestArray */
    public function testGetDocument(LazyBSONArray $array): void
    {
        $this->assertInstanceOf(LazyBSONDocument::class, $array[1]);
        $this->assertInstanceOf(LazyBSONDocument::class, $array[1]);
    }

    /** @dataProvider provideTestArray */
    public function testGetArray(LazyBSONArray $array): void
    {
        $this->assertInstanceOf(LazyBSONArray::class, $array[2]);
        $this->assertInstanceOf(LazyBSONArray::class, $array[2]);
    }

    /** @dataProvider provideTestArray */
    public function testOffsetExists(LazyBSONArray $array): void
    {
        $this->assertTrue(isset($array[0]));
        $this->assertFalse(isset($array[4]));
    }

    /** @dataProvider provideTestArray */
    public function testOffsetSet(LazyBSONArray $array): void
    {
        $this->assertFalse(isset($array[4]));
        $array[4] = 'yay!';
        $this->assertSame('yay!', $array[4]);

        $this->assertSame('bar', $array[0]);
        $array[0] = 'baz';
        $this->assertSame('baz', $array[0]);
    }

    /** @dataProvider provideTestArray */
    public function testAppend(LazyBSONArray $array): void
    {
        $this->assertFalse(isset($array[3]));
        $array[] = 'yay!';
        $this->assertSame('yay!', $array[3]);
    }

    /** @dataProvider provideTestArray */
    public function testAppendWithGap(LazyBSONArray $array): void
    {
        // Leave offset 3 empty
        $array[4] = 'yay!';

        $this->assertFalse(isset($array[3]));
        $array[] = 'bleh';

        // Expect offset 3 to be skipped, offset 5 is used as 4 is already set
        $this->assertFalse(isset($array[3]));
        $this->assertSame('bleh', $array[5]);
    }

    /** @dataProvider provideTestArray */
    public function testOffsetUnset(LazyBSONArray $array): void
    {
        $this->assertFalse(isset($array[4]));
        $array[4] = 'yay!';
        unset($array[4]);
        $this->assertFalse(isset($array[4]));

        unset($array[0]);
        $this->assertFalse(isset($array[0]));

        // Change value to ensure it is unset for good
        $array[1] = (object) ['foo' => 'baz'];
        unset($array[1]);
        $this->assertFalse(isset($array[1]));
    }

    /** @dataProvider provideTestArray */
    public function testIterator(LazyBSONArray $array): void
    {
        $items = iterator_to_array($array);
        $this->assertCount(3, $items);
        $this->assertSame('bar', $items[0]);
        $this->assertInstanceOf(LazyBSONDocument::class, $items[1]);
        $this->assertInstanceOf(LazyBSONArray::class, $items[2]);

        $array[0] = 'baz';
        $items = iterator_to_array($array);
        $this->assertCount(3, $items);
        $this->assertSame('baz', $items[0]);
        $this->assertInstanceOf(LazyBSONDocument::class, $items[1]);
        $this->assertInstanceOf(LazyBSONArray::class, $items[2]);

        unset($array[0]);
        unset($array[2]);
        $items = iterator_to_array($array);
        $this->assertCount(1, $items);
        $this->assertInstanceOf(LazyBSONDocument::class, $items[0]);

        // Leave a gap to ensure we're re-indexing keys
        $array[5] = 'yay!';
        $items = iterator_to_array($array);
        $this->assertCount(2, $items);
        $this->assertInstanceOf(LazyBSONDocument::class, $items[0]);
        $this->assertSame('yay!', $items[1]);
    }

    public function testJsonSerialize(): void
    {
        $document = new LazyBSONArray([
            'bar',
            new LazyBSONArray([1, 2, 3]),
            new LazyBSONDocument([1, 2, 3]),
            new LazyBSONArray([]),
        ]);

        $expectedJson = '["bar",[1,2,3],{"0":1,"1":2,"2":3},[]]';

        $this->assertSame($expectedJson, json_encode($document));
    }
}
