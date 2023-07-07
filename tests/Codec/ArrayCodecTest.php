<?php

namespace MongoDB\Tests\Codec;

use MongoDB\Codec\ArrayCodec;
use MongoDB\Codec\Codec;
use MongoDB\Codec\CodecLibrary;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Tests\TestCase;

class ArrayCodecTest extends TestCase
{
    public function testDecodeList(): void
    {
        $value = [
            'decoded',
            'encoded',
        ];

        $this->assertSame(['decoded', 'decoded'], $this->getCodec()->decode($value));
    }

    public function testDecodeListWithGaps(): void
    {
        $value = [
            0 => 'decoded',
            2 => 'encoded',
        ];

        $this->assertSame([0 => 'decoded', 2 => 'decoded'], $this->getCodec()->decode($value));
    }

    public function testDecodeHash(): void
    {
        $value = [
            'foo' => 'decoded',
            'bar' => 'encoded',
        ];

        $this->assertSame(['foo' => 'decoded', 'bar' => 'decoded'], $this->getCodec()->decode($value));
    }

    public function testDecodeWithWrongType(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue('foo'));
        $this->getCodec()->encode('foo');
    }

    public function testEncode(): void
    {
        $value = [
            'decoded',
            'encoded',
        ];

        $this->assertSame(['encoded', 'encoded'], $this->getCodec()->encode($value));
    }

    public function testEncodeListWithGaps(): void
    {
        $value = [
            0 => 'decoded',
            2 => 'encoded',
        ];

        $this->assertSame([0 => 'encoded', 2 => 'encoded'], $this->getCodec()->encode($value));
    }

    public function testEncodeHash(): void
    {
        $value = [
            'foo' => 'decoded',
            'bar' => 'encoded',
        ];

        $this->assertSame(['foo' => 'encoded', 'bar' => 'encoded'], $this->getCodec()->encode($value));
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
