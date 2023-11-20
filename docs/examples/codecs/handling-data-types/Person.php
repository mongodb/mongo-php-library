<?php

use MongoDB\BSON\ObjectId;

final class Person
{
    public function __construct(
        public string $name,
        public readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
        public readonly ObjectId $id = new ObjectId(),
    ) {
    }
}
