<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use MongoDB\Builder\Type\FieldPathInterface;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\Encoder;
use MongoDB\Exception\UnsupportedValueException;

/**
 * @template-implements Encoder<string, FieldPathInterface>
 * @internal
 */
final class FieldPathEncoder implements Encoder
{
    /** @template-use EncodeIfSupported<string, FieldPathInterface> */
    use EncodeIfSupported;

    public function canEncode(mixed $value): bool
    {
        return $value instanceof FieldPathInterface;
    }

    public function encode(mixed $value): string
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return '$' . $value->name;
    }
}
