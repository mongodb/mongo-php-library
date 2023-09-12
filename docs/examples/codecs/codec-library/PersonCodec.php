<?php

use MongoDB\BSON\Document;
use MongoDB\Codec\CodecLibrary;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;

final class PersonCodec implements DocumentCodec
{
    use DecodeIfSupported;
    use EncodeIfSupported;

    public function __construct(
        private readonly CodecLibrary $codecLibrary,
    ) {}

    public function canDecode($value): bool
    {
        return $value instanceof Document && $value->has('name');
    }

    public function canEncode($value): bool
    {
        return $value instanceof Person;
    }

    public function decode($value): Person
    {
        if (! $this->canDecode($value)) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        return new Person(
            $value->get('name'),
            $this->codecLibrary->decode($value->get('createdAt')),
            $value->get('_id'),
        );
    }

    public function encode($value): Document
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return Document::fromPHP([
            '_id' => $value->id,
            'name' => $value->name,
            'createdAt' => $this->codecLibrary->encode($value->createdAt),
        ]);
    }
}
