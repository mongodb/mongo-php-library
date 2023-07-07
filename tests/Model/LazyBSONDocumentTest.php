<?php

namespace MongoDB\Tests\Model;

use Generator;
use MongoDB\BSON\Document;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\LazyBSONArray;
use MongoDB\Model\LazyBSONDocument;
use MongoDB\Tests\TestCase;
use stdClass;

use function iterator_to_array;

class LazyBSONDocumentTest extends TestCase
{
    public static function provideTestDocument(): Generator
    {
        yield 'array' => [
            new LazyBSONDocument([
                'foo' => 'bar',
                'document' => new LazyBSONDocument(['bar' => 'baz']),
                'array' => new LazyBSONArray([0, 1, 2]),
            ]),
        ];

        yield 'object' => [
            new LazyBSONDocument((object) [
                'foo' => 'bar',
                'document' => new LazyBSONDocument(['bar' => 'baz']),
                'array' => new LazyBSONArray([0, 1, 2]),
            ]),
        ];

        yield 'document' => [
            new LazyBSONDocument(Document::fromPHP([
                'foo' => 'bar',
                'document' => ['bar' => 'baz'],
                'array' => [0, 1, 2],
            ])),
        ];
    }

    public static function provideTestDocumentWithNativeArrays(): Generator
    {
        yield 'array' => [
            new LazyBSONDocument([
                'document' => (object) ['bar' => 'baz'],
                'hash' => ['bar' => 'baz'],
                'array' => [0, 1, 2],
            ]),
        ];

        yield 'object' => [
            new LazyBSONDocument((object) [
                'document' => (object) ['bar' => 'baz'],
                'hash' => ['bar' => 'baz'],
                'array' => [0, 1, 2],
            ]),
        ];
    }

    public function testConstructWithoutArgument(): void
    {
        $instance = new LazyBSONDocument();
        $this->assertSame([], iterator_to_array($instance));
    }

    public function testConstructWithWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new LazyBSONDocument('foo');
    }

    /** @dataProvider provideTestDocumentWithNativeArrays */
    public function testConstructWithArrayUsesLiteralValues($value): void
    {
        $document = new LazyBSONDocument($value);

        $this->assertInstanceOf(stdClass::class, $document->document);
        $this->assertIsArray($document->hash);
        $this->assertIsArray($document->array);
    }

    public function testClone(): void
    {
        $original = new LazyBSONDocument();
        $original->object = (object) ['foo' => 'bar'];

        $clone = clone $original;
        $clone->object->foo = 'baz';

        self::assertSame('bar', $original->object->foo);
    }

    /** @dataProvider provideTestDocument */
    public function testPropertyGet(LazyBSONDocument $document): void
    {
        $this->assertSame('bar', $document->foo);
    }

    /** @dataProvider provideTestDocument */
    public function testPropertyGetAfterUnset(LazyBSONDocument $document): void
    {
        $this->assertSame('bar', $document->foo);
        unset($document->foo);

        $this->expectWarning();
        $this->expectWarningMessage('Undefined property: foo');
        $document->foo;
    }

    /** @dataProvider provideTestDocument */
    public function testPropertyGetForMissingProperty(LazyBSONDocument $document): void
    {
        $this->expectWarning();
        $this->expectWarningMessage('Undefined property: bar');
        $document->bar;
    }

    /** @dataProvider provideTestDocument */
    public function testOffsetGet(LazyBSONDocument $document): void
    {
        $this->assertSame('bar', $document['foo']);
    }

    /** @dataProvider provideTestDocument */
    public function testOffsetGetAfterUnset(LazyBSONDocument $document): void
    {
        $this->assertSame('bar', $document['foo']);
        unset($document['foo']);

        $this->expectWarning();
        $this->expectWarningMessage('Undefined property: foo');
        $document['foo'];
    }

    /** @dataProvider provideTestDocument */
    public function testOffsetGetForMissingOffset(LazyBSONDocument $document): void
    {
        $this->expectWarning();
        $this->expectWarningMessage('Undefined property: bar');
        $document['bar'];
    }

    /** @dataProvider provideTestDocument */
    public function testGetDocument(LazyBSONDocument $document): void
    {
        $this->assertInstanceOf(LazyBSONDocument::class, $document->document);
        $this->assertInstanceOf(LazyBSONDocument::class, $document['document']);
    }

    /** @dataProvider provideTestDocument */
    public function testGetArray(LazyBSONDocument $document): void
    {
        $this->assertInstanceOf(LazyBSONArray::class, $document->array);
        $this->assertInstanceOf(LazyBSONArray::class, $document['array']);
    }

    /** @dataProvider provideTestDocument */
    public function testPropertyIsset(LazyBSONDocument $document): void
    {
        $this->assertTrue(isset($document->foo));
        $this->assertFalse(isset($document->bar));
    }

    /** @dataProvider provideTestDocument */
    public function testOffsetExists(LazyBSONDocument $document): void
    {
        $this->assertTrue(isset($document['foo']));
        $this->assertFalse(isset($document['bar']));
    }

    /** @dataProvider provideTestDocument */
    public function testPropertySet(LazyBSONDocument $document): void
    {
        $this->assertFalse(isset($document->new));
        $document->new = 'yay!';
        $this->assertSame('yay!', $document->new);

        $this->assertSame('bar', $document->foo);
        $document->foo = 'baz';
        $this->assertSame('baz', $document->foo);
    }

    /** @dataProvider provideTestDocument */
    public function testOffsetSet(LazyBSONDocument $document): void
    {
        $this->assertFalse(isset($document['new']));
        $document['new'] = 'yay!';
        $this->assertSame('yay!', $document['new']);

        $this->assertSame('bar', $document['foo']);
        $document['foo'] = 'baz';
        $this->assertSame('baz', $document['foo']);
    }

    /** @dataProvider provideTestDocument */
    public function testPropertyUnset(LazyBSONDocument $document): void
    {
        $this->assertFalse(isset($document->new));
        $document->new = 'yay!';
        unset($document->new);
        $this->assertFalse(isset($document->new));

        unset($document->foo);
        $this->assertFalse(isset($document->foo));

        // Change value to ensure it is unset for good
        $document->document = (object) ['foo' => 'baz'];
        unset($document->document);
        $this->assertFalse(isset($document->document));
    }

    /** @dataProvider provideTestDocument */
    public function testOffsetUnset(LazyBSONDocument $document): void
    {
        $this->assertFalse(isset($document['new']));
        $document['new'] = 'yay!';
        unset($document['new']);
        $this->assertFalse(isset($document['new']));

        unset($document['foo']);
        $this->assertFalse(isset($document['foo']));

        // Change value to ensure it is unset for good
        $document['document'] = (object) ['foo' => 'baz'];
        unset($document['document']);
        $this->assertFalse(isset($document['document']));
    }

    /** @dataProvider provideTestDocument */
    public function testIterator(LazyBSONDocument $document): void
    {
        $items = iterator_to_array($document);
        $this->assertCount(3, $items);
        $this->assertSame('bar', $items['foo']);
        $this->assertInstanceOf(LazyBSONDocument::class, $items['document']);
        $this->assertInstanceOf(LazyBSONArray::class, $items['array']);

        $document->foo = 'baz';
        $items = iterator_to_array($document);
        $this->assertCount(3, $items);
        $this->assertSame('baz', $items['foo']);
        $this->assertInstanceOf(LazyBSONDocument::class, $items['document']);
        $this->assertInstanceOf(LazyBSONArray::class, $items['array']);

        unset($document->foo);
        unset($document->array);
        $items = iterator_to_array($document);
        $this->assertCount(1, $items);
        $this->assertInstanceOf(LazyBSONDocument::class, $items['document']);

        $document->new = 'yay!';
        $items = iterator_to_array($document);
        $this->assertCount(2, $items);
        $this->assertInstanceOf(LazyBSONDocument::class, $items['document']);
        $this->assertSame('yay!', $items['new']);
    }
}
