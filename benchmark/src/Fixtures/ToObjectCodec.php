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

    public function canDecode(mixed $value): bool
    {
        return $value instanceof Document;
    }

    public function canEncode(mixed $value): bool
    {
        return is_object($value);
    }

    public function decode(mixed $value): object
    {
        if (! $value instanceof Document) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        return $value->toPHP(['root' => 'stdClass', 'array' => 'array', 'document' => 'stdClass']);
    }

    public function encode(mixed $value): Document
    {
        if (! is_object($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return Document::fromPHP($value);
    }
}
