<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use MongoDB\Builder\Expression\Variable;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;

/** @template-extends AbstractExpressionEncoder<string, Variable> */
class VariableEncoder extends AbstractExpressionEncoder
{
    /** @template-use EncodeIfSupported<string, Variable> */
    use EncodeIfSupported;

    public function canEncode(mixed $value): bool
    {
        return $value instanceof Variable;
    }

    public function encode(mixed $value): string
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        // TODO: needs method because interfaces can't have properties
        return '$$' . $value->name;
    }
}
