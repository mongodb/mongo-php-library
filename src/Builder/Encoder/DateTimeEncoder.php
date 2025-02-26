<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use DateTimeInterface;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;

/**
 * @template-extends AbstractExpressionEncoder<UTCDateTime, DateTimeInterface>
 * @internal
 */
final class DateTimeEncoder extends AbstractExpressionEncoder
{
    /** @template-use EncodeIfSupported<UTCDateTime, DateTimeInterface> */
    use EncodeIfSupported;

    /** @psalm-assert-if-true DateTimeInterface $value */
    public function canEncode(mixed $value): bool
    {
        return $value instanceof DateTimeInterface;
    }

    public function encode(mixed $value): UTCDateTime
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return new UTCDateTime($value);
    }
}
