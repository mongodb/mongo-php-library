<?php

use MongoDB\BSON\Document;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Codec\Codec;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;

/** @template-implements Codec<Document, DateTimeImmutable> */
final class DateTimeCodec implements Codec
{
    use DecodeIfSupported;
    use EncodeIfSupported;

    public function canDecode(mixed $value): bool
    {
        /* This codec inspects the BSON document to ensure it has the fields it expects, and that those fields are of
         * the correct type. This is a robust approach to avoid decoding document that are not supported and would cause
         * exceptions.
         *
         * For large documents, this can be inefficient as we're inspecting the entire document four times (once for
         * each call to has() and get()). For small documents, this is not a problem.
         */
        return $value instanceof Document
            && $value->has('utc') && $value->get('utc') instanceof UTCDateTime
            && $value->has('tz') && is_string($value->get('tz'));
    }

    public function canEncode(mixed $value): bool
    {
        return $value instanceof DateTimeInterface;
    }

    public function decode(mixed $value): DateTimeImmutable
    {
        if (! $this->canDecode($value)) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        $timeZone = new DateTimeZone($value->get('tz'));
        $dateTime = $value->get('utc')
            ->toDateTime()
            ->setTimeZone($timeZone);

        return DateTimeImmutable::createFromMutable($dateTime);
    }

    public function encode(mixed $value): Document
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
