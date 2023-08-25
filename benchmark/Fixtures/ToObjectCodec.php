<?php

namespace MongoDB\Benchmark\Fixtures;

use MongoDB\BSON\Document;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;

use function is_object;

final class ToObjectCodec implements DocumentCodec
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
        return is_object($value);
    }

    /** @param mixed $value */
    public function decode($value): object
    {
        if (! $value instanceof Document) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        return $value->toPHP(['root' => 'stdClass', 'array' => 'array', 'document' => 'stdClass']);
    }

    /** @param mixed $value */
    public function encode($value): Document
    {
        if (! is_object($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return Document::fromPHP($value);
    }
}
