<?php

final class Person
{
    public ?Address $address = null;

    public function __construct(
        public string $name,
        public readonly MongoDB\BSON\ObjectId $id = new MongoDB\BSON\ObjectId(),
       ) {
    }
}
