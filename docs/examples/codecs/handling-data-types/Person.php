<?php

final class Person
{
    public function __construct(
        public string $name,
        public readonly DateTime $createdAt = new DateTime(),
        public readonly MongoDB\BSON\ObjectId $id = new MongoDB\BSON\ObjectId(),
       ) {
    }
}
