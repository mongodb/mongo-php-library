<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use MongoDB\Builder\Type\DictionaryInterface;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;
use stdClass;

/** @template-extends AbstractExpressionEncoder<string|int|array|stdClass, DictionaryInterface> */
class DictionaryEncoder extends AbstractExpressionEncoder
{
    /** @template-use EncodeIfSupported<string|int|array|stdClass, DictionaryInterface> */
    use EncodeIfSupported;

    public function canEncode(mixed $value): bool
    {
        return $value instanceof DictionaryInterface;
    }

    public function encode(mixed $value): string|int|array|stdClass
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return $value->getValue();
    }
}
