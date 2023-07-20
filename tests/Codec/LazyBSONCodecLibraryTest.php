<?php

namespace MongoDB\Tests\Codec;

use Generator;
use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\Codec\LazyBSONCodecLibrary;
use MongoDB\Model\LazyBSONArray;
use MongoDB\Model\LazyBSONDocument;
use MongoDB\Tests\TestCase;

class LazyBSONCodecLibraryTest extends TestCase
{
    public static function provideDecodedData(): Generator
    {
        $array = [
            'bar',
            ['foo' => 'bar'],
            [0, 1, 2],
        ];
        $document = (object) [
            'string' => 'bar',
            'document' => (object) ['foo' => 'bar'],
            'array' => [0, 1, 2],
        ];

        yield 'LazyBSONArray' => [
            'expected' => PackedArray::fromPHP($array),
            'value' => new LazyBSONArray($array),
        ];

        yield 'LazyBSONDocument' => [
            'expected' => Document::fromPHP($document),
            'value' => new LazyBSONDocument($document),
        ];

        yield 'array' => [
            'expected' => [PackedArray::fromPHP($array)],
            'value' => [new LazyBSONArray($array)],
        ];

        yield 'hash' => [
            'expected' => ['foo' => PackedArray::fromPHP($array)],
            'value' => ['foo' => new LazyBSONArray($array)],
        ];

        yield 'object' => [
            'expected' => (object) ['foo' => PackedArray::fromPHP($array)],
            'value' => (object) ['foo' => new LazyBSONArray($array)],
        ];
    }

    public static function provideEncodedData(): Generator
    {
        $packedArray = PackedArray::fromPHP([
            'bar',
            ['foo' => 'bar'],
            [0, 1, 2],
        ]);
        $document = Document::fromPHP([
            'string' => 'bar',
            'document' => ['foo' => 'bar'],
            'array' => [0, 1, 2],
        ]);

        yield 'packedArray' => [
            'expected' => new LazyBSONArray($packedArray),
            'value' => $packedArray,
        ];

        yield 'document' => [
            'expected' => new LazyBSONDocument($document),
            'value' => $document,
        ];

        yield 'array' => [
            'expected' => [new LazyBSONArray($packedArray)],
            'value' => [$packedArray],
        ];

        yield 'hash' => [
            'expected' => ['foo' => new LazyBSONArray($packedArray)],
            'value' => ['foo' => $packedArray],
        ];

        yield 'object' => [
            'expected' => (object) ['foo' => new LazyBSONArray($packedArray)],
            'value' => (object) ['foo' => $packedArray],
        ];
    }

    /** @dataProvider provideEncodedData */
    public function testDecode($expected, $value): void
    {
        $this->assertEquals($expected, (new LazyBSONCodecLibrary())->decode($value));
    }

    /** @dataProvider provideDecodedData */
    public function testEncode($expected, $value): void
    {
        $this->assertEquals($expected, (new LazyBSONCodecLibrary())->encode($value));
    }
}
