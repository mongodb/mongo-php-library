<?php

namespace MongoDB\Tests\Codec;

use Generator;
use MongoDB\Codec\ArrayCodec;
use MongoDB\Codec\Codec;
use MongoDB\Codec\CodecLibrary;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Tests\TestCase;

class ArrayCodecTest extends TestCase
{
    public static function provideValues(): Generator
    {
        yield 'List' => [
            'value' => ['decoded', 'encoded'],
            'encoded' => ['encoded', 'encoded'],
            'decoded' => ['decoded', 'decoded'],
        ];

        yield 'List with gaps' => [
            'value' => [0 => 'decoded', 2 => 'encoded'],
            'encoded' => [0 => 'encoded', 2 => 'encoded'],
            'decoded' => [0 => 'decoded', 2 => 'decoded'],
        ];

        yield 'Hash' => [
            'value' => ['foo' => 'decoded', 'bar' => 'encoded'],
            'encoded' => ['foo' => 'encoded', 'bar' => 'encoded'],
            'decoded' => ['foo' => 'decoded', 'bar' => 'decoded'],
        ];
    }

    /** @dataProvider provideValues */
    public function testDecode($value, $encoded, $decoded): void
    {
        $this->assertSame(
            $decoded,
            $this->getCodec()->decode($value),
        );
    }

    /** @dataProvider provideValues */
    public function testEncode($value, $encoded, $decoded): void
    {
        $this->assertSame(
            $encoded,
            $this->getCodec()->encode($value),
        );
    }

    public function testDecodeWithWrongType(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue('foo'));
        $this->getCodec()->encode('foo');
    }

    public function testEncodeWithWrongType(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidDecodableValue('foo'));
        $this->getCodec()->decode('foo');
    }

    private function getCodec(): ArrayCodec
    {
        $arrayCodec = new ArrayCodec();
        $arrayCodec->attachCodecLibrary($this->getCodecLibrary());

        return $arrayCodec;
    }

    private function getCodecLibrary(): CodecLibrary
    {
        return new CodecLibrary(
            /** @template-implements Codec<string, string> */
            new class implements Codec
            {
                use DecodeIfSupported;
                use EncodeIfSupported;

                public function canDecode($value): bool
                {
                    return $value === 'encoded';
                }

                public function canEncode($value): bool
                {
                    return $value === 'decoded';
                }

                public function decode($value)
                {
                    return 'decoded';
                }

                public function encode($value)
                {
                    return 'encoded';
                }
            },
        );
    }
}
