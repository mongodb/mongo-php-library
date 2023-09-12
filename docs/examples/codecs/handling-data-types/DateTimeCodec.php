<?php

use MongoDB\BSON\Document;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Codec\Codec;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;

final class DateTimeCodec implements Codec
{
    use DecodeIfSupported;
    use EncodeIfSupported;

    public function canDecode($value): bool
    {
        // For maximum compatibility, this codec supports decoding both UTCDateTime instances and documents with a
        // UTCDateTime instance and optional timezone
        return
            $value instanceof UTCDateTime ||
            $value instanceof Document && $value->has('utc');
    }

    public function canEncode($value): bool
    {
        return $value instanceof DateTimeInterface;
    }

    public function decode($value): DateTimeImmutable
    {
        if (! $this->canDecode($value)) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        $utc = $value instanceof UTCDateTime
            ? $value
            : $value->get('utc');

        if (! $utc instanceof UTCDateTime) {
            throw UnsupportedValueException::invalidDecodableValue($utc);
        }

        $dateTime = $utc->toDateTime();

        if ($value instanceof Document && $value->has('tz')) {
            $dateTime->setTimeZone(new DateTimeZone($value->get('tz')));
        }

        return DateTimeImmutable::createFromMutable($dateTime);
    }

    public function encode($value): Document
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return Document::fromPHP([
            'utc' => new UTCDateTime($value),
            'tz' => $value->getTimezone()->getName(),
        ]);
    }
}
