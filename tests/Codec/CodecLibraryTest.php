<?php

namespace MongoDB\Tests\Codec;

use MongoDB\Codec\Codec;
use MongoDB\Codec\CodecLibrary;
use MongoDB\Codec\CodecLibraryAware;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Tests\TestCase;

class CodecLibraryTest extends TestCase
{
    public function testDecode(): void
    {
        $codec = $this->getCodecLibrary();

        $this->assertTrue($codec->canDecode('encoded'));
        $this->assertFalse($codec->canDecode('decoded'));

        $this->assertSame('decoded', $codec->decode('encoded'));
    }

    public function testDecodeIfSupported(): void
    {
        $codec = $this->getCodecLibrary();

        $this->assertSame('decoded', $codec->decodeIfSupported('encoded'));
        $this->assertSame('decoded', $codec->decodeIfSupported('decoded'));
    }

    public function testDecodeNull(): void
    {
        $codec = $this->getCodecLibrary();

        $this->assertFalse($codec->canDecode(null));

        $this->expectExceptionObject(UnsupportedValueException::invalidDecodableValue(null));
        $codec->decode(null);
    }

    public function testDecodeUnsupportedValue(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidDecodableValue('foo'));
        $this->getCodecLibrary()->decode('foo');
    }

    public function testEncode(): void
    {
        $codec = $this->getCodecLibrary();

        $this->assertTrue($codec->canEncode('decoded'));
        $this->assertFalse($codec->canEncode('encoded'));

        $this->assertSame('encoded', $codec->encode('decoded'));
    }

    public function testEncodeIfSupported(): void
    {
        $codec = $this->getCodecLibrary();

        $this->assertSame('encoded', $codec->encodeIfSupported('decoded'));
        $this->assertSame('encoded', $codec->encodeIfSupported('encoded'));
    }

    public function testEncodeNull(): void
    {
        $codec = $this->getCodecLibrary();

        $this->assertFalse($codec->canEncode(null));

        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue(null));
        $codec->encode(null);
    }

    public function testEncodeUnsupportedValue(): void
    {
        $this->expectExceptionObject(UnsupportedValueException::invalidEncodableValue('foo'));
        $this->getCodecLibrary()->encode('foo');
    }

    public function testLibraryAttachesToCodecs(): void
    {
        // TODO PHPUnit >= 10: use createMockForIntersectionOfInterfaces instead
        $codec = $this->getTestCodec();
        $library = $this->getCodecLibrary();

        $library->attachCodec($codec);
        $this->assertSame($library, $codec->library);
    }

    public function testLibraryAttachesToCodecsWhenCreating(): void
    {
        $codec = $this->getTestCodec();
        $library = new CodecLibrary($codec);

        $this->assertSame($library, $codec->library);
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

    private function getTestCodec(): Codec
    {
        return new class implements Codec, CodecLibraryAware {
            use DecodeIfSupported;
            use EncodeIfSupported;

            public $library;

            public function attachCodecLibrary(CodecLibrary $library): void
            {
                $this->library = $library;
            }

            public function canDecode($value): bool
            {
                return false;
            }

            public function canEncode($value): bool
            {
                return false;
            }

            public function decode($value)
            {
                return null;
            }

            public function encode($value)
            {
                return null;
            }
        };
    }
}
