<?php

namespace MongoDB\Tests\Codec;

use MongoDB\Codec\Codec;
use MongoDB\Codec\CodecLibrary;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\KnowsCodecLibrary;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Tests\TestCase;

class CodecLibraryTest extends TestCase
{
    public function testDecode(): void
    {
        $codec = $this->getCodecLibrary();

        $this->assertTrue($codec->canDecode('cigam'));
        $this->assertFalse($codec->canDecode('magic'));

        $this->assertSame('magic', $codec->decode('cigam'));
    }

    public function testDecodeIfSupported(): void
    {
        $codec = $this->getCodecLibrary();

        $this->assertSame('magic', $codec->decodeIfSupported('cigam'));
        $this->assertSame('magic', $codec->decodeIfSupported('magic'));
    }

    public function testDecodeNull(): void
    {
        $codec = $this->getCodecLibrary();

        $this->assertFalse($codec->canDecode(null));

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('No decoder found for value of type "null"');

        $this->assertNull($codec->decode(null));
    }

    public function testDecodeUnsupportedValue(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('No decoder found for value of type "string"');

        $this->getCodecLibrary()->decode('foo');
    }

    public function testEncode(): void
    {
        $codec = $this->getCodecLibrary();

        $this->assertTrue($codec->canEncode('magic'));
        $this->assertFalse($codec->canEncode('cigam'));

        $this->assertSame('cigam', $codec->encode('magic'));
    }

    public function testEncodeIfSupported(): void
    {
        $codec = $this->getCodecLibrary();

        $this->assertSame('cigam', $codec->encodeIfSupported('magic'));
        $this->assertSame('cigam', $codec->encodeIfSupported('cigam'));
    }

    public function testEncodeNull(): void
    {
        $codec = $this->getCodecLibrary();

        $this->assertFalse($codec->canEncode(null));

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('No encoder found for value of type "null"');

        $codec->encode(null);
    }

    public function testEncodeUnsupportedValue(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('No encoder found for value of type "string"');

        $this->getCodecLibrary()->encode('foo');
    }

    public function testLibraryAttachesToCodecs(): void
    {
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
                    return $value === 'cigam';
                }

                public function canEncode($value): bool
                {
                    return $value === 'magic';
                }

                public function decode($value)
                {
                    return 'magic';
                }

                public function encode($value)
                {
                    return 'cigam';
                }
            }
        );
    }

    private function getTestCodec(): Codec
    {
        return new class implements Codec, KnowsCodecLibrary {
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
