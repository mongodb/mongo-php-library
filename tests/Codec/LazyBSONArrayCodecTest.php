<?php

namespace MongoDB\Tests\Codec;

use MongoDB\BSON\PackedArray;
use MongoDB\Codec\LazyBSONArrayCodec;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Model\LazyBSONArray;
use MongoDB\Tests\TestCase;

class LazyBSONArrayCodecTest extends TestCase
{
    private const ARRAY = [
        'bar',
        ['bar' => 'baz'],
        [0, 1, 2],
    ];

    public function testDecode(): void
    {
        $array = (new LazyBSONArrayCodec())->decode($this->getTestArray());

        $this->assertInstanceOf(LazyBSONArray::class, $array);
        $this->assertSame('bar', $array[0]);
    }

    public function testDecodeWithWrongType(): void
    {
        $codec = new LazyBSONArrayCodec();

        $this->expectExceptionObject(UnsupportedValueException::invalidDecodableValue('foo'));
        $codec->decode('foo');
    }

    public function testEncode(): void
    {
        $array = new LazyBSONArray($this->getTestArray());
        $encoded = (new LazyBSONArrayCodec())->encode($array);

        $this->assertEquals(
            self::ARRAY,
            $encoded->toPHP(['root' => 'array', 'array' => 'array', 'document' => 'array']),
        );
    }

    public function testEncodeWithWrongType(): void
    {
        $codec = new LazyBSONArrayCodec();

        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue('foo'));
        $codec->encode('foo');
    }

    private function getTestArray(): PackedArray
    {
        return PackedArray::fromPHP(self::ARRAY);
    }
}
