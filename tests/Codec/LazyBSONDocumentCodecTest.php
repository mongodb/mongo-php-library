<?php

namespace MongoDB\Tests\Codec;

use MongoDB\BSON\Document;
use MongoDB\Codec\LazyBSONDocumentCodec;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Model\LazyBSONDocument;
use MongoDB\Tests\TestCase;

class LazyBSONDocumentCodecTest extends TestCase
{
    private const OBJECT = [
        'foo' => 'bar',
        'document' => ['bar' => 'baz'],
        'array' => [0, 1, 2],
    ];

    public function testDecode(): void
    {
        $document = (new LazyBSONDocumentCodec())->decode($this->getTestDocument());

        $this->assertInstanceOf(LazyBSONDocument::class, $document);
        $this->assertSame('bar', $document->foo);
    }

    public function testDecodeWithWrongType(): void
    {
        $codec = new LazyBSONDocumentCodec();

        $this->expectExceptionObject(UnsupportedValueException::invalidDecodableValue('foo'));
        $codec->decode('foo');
    }

    public function testEncode(): void
    {
        $document = new LazyBSONDocument($this->getTestDocument());
        $encoded = (new LazyBSONDocumentCodec())->encode($document);

        $this->assertEquals(
            self::OBJECT,
            $encoded->toPHP(['root' => 'array', 'array' => 'array', 'document' => 'array']),
        );
    }

    public function testEncodeWithWrongType(): void
    {
        $codec = new LazyBSONDocumentCodec();

        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue('foo'));
        $codec->encode('foo');
    }

    private function getTestDocument(): Document
    {
        return Document::fromPHP(self::OBJECT);
    }
}
