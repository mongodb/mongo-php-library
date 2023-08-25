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

    /** @param mixed $value */
    public function canDecode($value): bool
    {
        return $value instanceof Document;
    }

    /** @param mixed $value */
    public function canEncode($value): bool
    {
        return $value instanceof Document;
    }

    /** @param mixed $value */
    public function decode($value): Document
    {
        if (! $value instanceof Document) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        return $value;
    }

    /** @param mixed $value */
    public function encode($value): Document
    {
        if (! $value instanceof Document) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return $value;
    }
}
