<?php

use MongoDB\BSON\ObjectId;

final class Person
{
    public function __construct(
        public string $name,
        public readonly DateTime $createdAt = new DateTime(),
        public readonly ObjectId $id = new ObjectId(),
    ) {
    }
}
