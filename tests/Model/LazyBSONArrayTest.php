<?php

namespace MongoDB\Tests\Model;

use Generator;
use MongoDB\BSON\PackedArray;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\LazyBSONArray;
use MongoDB\Model\LazyBSONDocument;
use MongoDB\Tests\TestCase;
use stdClass;

use function iterator_to_array;
use function json_encode;
use function serialize;
use function unserialize;

use const JSON_THROW_ON_ERROR;

class LazyBSONArrayTest extends TestCase
{
    public static function provideTestArray(): Generator
    {
        yield 'array' => [
            new LazyBSONArray([
                'bar',
                new LazyBSONDocument(['bar' => 'baz']),
                new LazyBSONArray([0, 1, 2]),
            ]),
        ];

        yield 'packedArray' => [
            new LazyBSONArray(PackedArray::fromPHP([
                'bar',
                ['bar' => 'baz'],
                [0, 1, 2],
            ])),
        ];
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

    public function testConstructWithArrayUsesLiteralValues(): void
    {
        $array = new LazyBSONArray([
            (object) ['bar' => 'baz'],
            ['bar' => 'baz'],
            [0, 1, 2],
        ]);

        $this->assertInstanceOf(stdClass::class, $array[0]);
        $this->assertIsArray($array[1]);
        $this->assertIsArray($array[2]);
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
    public function testOffsetGetForNumericOffset(LazyBSONArray $array): void
    {
        $this->assertSame('bar', $array['0']);
    }

    /** @dataProvider provideTestArray */
    public function testOffsetGetForUnsupportedOffset(LazyBSONArray $array): void
    {
        $this->expectWarning();
        $this->expectWarningMessage('Undefined offset: foo');
        $array['foo'];
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

        // Unsupported offset
        $this->assertFalse(isset($array['foo']));

        // Numeric offset
        $this->assertTrue(isset($array['1']));
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
    public function testOffsetSetForNumericOffset(LazyBSONArray $array): void
    {
        $array['1'] = 'baz';
        $this->assertSame('baz', $array[1]);
    }

    /** @dataProvider provideTestArray */
    public function testOffsetSetForUnsupportedOffset(LazyBSONArray $array): void
    {
        $this->expectWarning();
        $this->expectWarningMessage('Unsupported offset: foo');
        $array['foo'] = 'yay!';
    }

    /** @dataProvider provideTestArray */
    public function testAppend(LazyBSONArray $array): void
    {
        $this->assertFalse(isset($array[3]));
        $array[] = 'yay!';
        $this->assertSame('yay!', $array[3]);
    }

    public function testAppendToEmptyArray(): void
    {
        $array = new LazyBSONArray();

        $this->assertFalse(isset($array[0]));
        $array[] = 'yay!';
        $this->assertSame('yay!', $array[0]);
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

    public function testCount(): void
    {
        $array = new LazyBSONArray(PackedArray::fromPHP(['foo', 'bar', 'baz']));

        $this->assertCount(3, $array);

        // Overwrite existing item, count must not change
        $array[0] = 'yay';
        $this->assertCount(3, $array);

        // Unset existing element, count must decrease
        unset($array[1]);
        $this->assertCount(2, $array);

        // Append element, count must increase again
        $array[] = 'yay';
        $this->assertCount(3, $array);
    }

    public function testSerialization(): void
    {
        $array = new LazyBSONArray(PackedArray::fromPHP(['foo', 'bar', 'baz']));
        $array[0] = 'foobar';
        $array[3] = 'yay!';
        unset($array[1]);

        $serialized = serialize($array);
        $unserialized = unserialize($serialized);

        $this->assertEquals(['foobar', 'baz', 'yay!'], iterator_to_array($unserialized));
    }

    public function testJsonSerialize(): void
    {
        $array = new LazyBSONArray(PackedArray::fromPHP(['foo', 'bar', 'baz']));
        $array[0] = 'foobar';
        $array[3] = 'yay!';
        unset($array[1]);

        $this->assertJsonStringEqualsJsonString('["foobar","baz","yay!"]', json_encode($array, JSON_THROW_ON_ERROR));
    }
}
