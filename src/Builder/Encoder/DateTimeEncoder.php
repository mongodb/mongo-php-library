<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use DateTimeInterface;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\Encoder;
use MongoDB\Exception\UnsupportedValueException;

/**
 * @template-implements Encoder<UTCDateTime, DateTimeInterface>
 * @internal
 */
final class DateTimeEncoder implements Encoder
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
