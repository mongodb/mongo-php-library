<?php

namespace MongoDB\Benchmark\Fixtures;

use MongoDB\BSON\Document;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;

final class PassThruCodec implements DocumentCodec
{
    use DecodeIfSupported;
    use EncodeIfSupported;

    public function canDecode(mixed $value): bool
    {
        return $value instanceof Document;
    }

    public function canEncode(mixed $value): bool
    {
        return $value instanceof Document;
    }

    public function decode(mixed $value): Document
    {
        if (! $value instanceof Document) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        return $value;
    }

    public function encode(mixed $value): Document
    {
        if (! $value instanceof Document) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return $value;
    }
}
