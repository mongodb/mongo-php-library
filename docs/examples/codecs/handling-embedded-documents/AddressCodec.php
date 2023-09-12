<?php

use MongoDB\BSON\Document;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;

final class AddressCodec implements DocumentCodec
{
    use DecodeIfSupported;
    use EncodeIfSupported;

    public function canDecode($value): bool
    {
        return $value instanceof Document
            && $value->has('street')
            && $value->has('postCode')
            && $value->has('city')
            && $value->has('country');
    }

    public function canEncode($value): bool
    {
        return $value instanceof Address;
    }

    public function decode($value): Address
    {
        if (! $this->canDecode($value)) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        return new Address(
            $value->get('street'),
            $value->get('postCode'),
            $value->get('city'),
            $value->get('country'),
        );
    }

    public function encode($value): Document
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return Document::fromPHP([
            'street' => $value->street,
            'postCode' => $value->postCode,
            'city' => $value->city,
            'country' => $value->country,
        ]);
    }
}

