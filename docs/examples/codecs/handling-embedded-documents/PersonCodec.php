<?php

use MongoDB\BSON\Document;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;

/** @template-implements DocumentCodec<Person> */
final class PersonCodec implements DocumentCodec
{
    use DecodeIfSupported;
    use EncodeIfSupported;

    public function __construct(
        private readonly AddressCodec $addressCodec = new AddressCodec(),
    ) {
    }

    public function canDecode(mixed $value): bool
    {
        return $value instanceof Document && $value->has('name');
    }

    public function canEncode(mixed $value): bool
    {
        return $value instanceof Person;
    }

    public function decode(mixed $value): Person
    {
        if (! $this->canDecode($value)) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        $person = new Person(
            $value->get('name'),
            $value->get('_id'),
        );

        // Address is optional, so only decode if it exists
        if ($value->has('address')) {
            $person->address = $this->addressCodec->decode($value->get('address'));
        }

        return $person;
    }

    public function encode(mixed $value): Document
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        $data = [
            '_id' => $value->id,
            'name' => $value->name,
        ];

        // Don't add a null value to the document if address is not set
        if ($value->address) {
            $data['address'] = $this->addressCodec->encode($value->address);
        }

        return Document::fromPHP($data);
    }
}
