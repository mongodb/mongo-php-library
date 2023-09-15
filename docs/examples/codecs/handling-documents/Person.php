<?php

use MongoDB\BSON\ObjectId;

final class Person
{
    public function __construct(
        public string $name,
        public readonly ObjectId $id = new ObjectId(),
    ) {
    }
}
