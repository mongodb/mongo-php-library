<?php

namespace MongoDB\BSON;

interface BSON
{
    static public function fromPHP(array $value): BSON {}

    public function getIterator(): Iterator {}

    public function toPHP(?array $typeMap = null): array|object {}

    public function __toString(): string {}
}
