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
            (object) ['foo' => 'bar'],
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
            'expected' => [PackedArray::fromPHP($array), Document::fromPHP($document)],
            'value' => [new LazyBSONArray($array), new LazyBSONDocument($document)],
        ];

        yield 'hash' => [
            'expected' => ['array' => PackedArray::fromPHP($array), 'document' => Document::fromPHP($document)],
            'value' => ['array' => new LazyBSONArray($array), 'document' => new LazyBSONDocument($document)],
        ];

        yield 'object' => [
            'expected' => (object) ['array' => PackedArray::fromPHP($array), 'document' => Document::fromPHP($document)],
            'value' => (object) ['array' => new LazyBSONArray($array), 'document' => new LazyBSONDocument($document)],
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

        yield 'PackedArray' => [
            'expected' => new LazyBSONArray($packedArray),
            'value' => $packedArray,
        ];

        yield 'Document' => [
            'expected' => new LazyBSONDocument($document),
            'value' => $document,
        ];

        yield 'array' => [
            'expected' => [new LazyBSONArray($packedArray), new LazyBSONDocument($document)],
            'value' => [$packedArray, $document],
        ];

        yield 'hash' => [
            'expected' => ['array' => new LazyBSONArray($packedArray), 'document' => new LazyBSONDocument($document)],
            'value' => ['array' => $packedArray, 'document' => $document],
        ];

        yield 'object' => [
            'expected' => (object) ['array' => new LazyBSONArray($packedArray), 'document' => new LazyBSONDocument($document)],
            'value' => (object) ['array' => $packedArray, 'document' => $document],
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
