<?php

namespace MongoDB\Tests\Codec;

use MongoDB\Codec\Codec;
use MongoDB\Codec\CodecLibrary;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\ObjectCodec;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Tests\TestCase;
use stdClass;

class ObjectCodecTest extends TestCase
{
    public function testDecodeObject(): void
    {
        $value = (object) [
            'foo' => 'decoded',
            'bar' => 'encoded',
        ];

        $this->assertEquals(
            (object) ['foo' => 'decoded', 'bar' => 'decoded'],
            $this->getCodec()->decode($value),
        );
    }

    public function testDecodeExtendedObject(): void
    {
        $value = $this->getExtendedObject();

        $this->assertEquals(
            (object) ['foo' => 'decoded', 'bar' => 'decoded'],
            $this->getCodec()->decode($value),
        );
    }

    public function testDecodeWithWrongType(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidDecodableValue('foo'));
        $this->getCodec()->decode('foo');
    }

    public function testEncodeObject(): void
    {
        $value = (object) [
            'foo' => 'decoded',
            'bar' => 'encoded',
        ];

        $this->assertEquals(
            (object) ['foo' => 'encoded', 'bar' => 'encoded'],
            $this->getCodec()->encode($value),
        );
    }

    public function testEncodeExtendedObject(): void
    {
        $value = $this->getExtendedObject();

        $this->assertEquals(
            (object) ['foo' => 'encoded', 'bar' => 'encoded'],
            $this->getCodec()->encode($value),
        );
    }

    public function testEncodeWithWrongType(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue('foo'));
        $this->getCodec()->encode('foo');
    }

    private function getCodec(): ObjectCodec
    {
        $objectCodec = new ObjectCodec();
        $objectCodec->attachCodecLibrary($this->getCodecLibrary());

        return $objectCodec;
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

    private function getExtendedObject(): stdClass
    {
        return new class extends stdClass {
            public static $baz = 'oops';
            public $foo = 'decoded';
            public $bar = 'encoded';
            protected $protected = 'oops';
            private string $private = 'oops';
        };
    }
}
