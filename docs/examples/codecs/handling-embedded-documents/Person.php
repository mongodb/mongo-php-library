<?php

use MongoDB\BSON\ObjectId;

final class Person
{
    public ?Address $address = null;

    public function __construct(
        public string $name,
        public readonly ObjectId $id = new ObjectId()
    ) {
    }
}
