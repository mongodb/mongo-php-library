<?php

namespace MongoDB\BSON;

final class Document implements BSON, \IteratorAggregate, \Serializable
{
    private function __construct() {}

    final static public function fromBSON(string $bson): Document {}

    final static public function fromJSON(string $json): Document {}

    final static public function fromPHP(array|object $value): Document {}

    final public function get(string $key): mixed {}

    final public function getIterator(): Iterator {}

    final public function has(string $key): bool {}

    final public function toPHP(?array $typeMap = null): array|object {}

    final public function toCanonicalExtendedJSON(): string {}

    final public function toRelaxedExtendedJSON(): string {}

    final public function __toString(): string {}

    final public static function __set_state(array $properties): Document {}

    final public function serialize(): string {}

    /** @param string $serialized */
    final public function unserialize($serialized): void {}

    final public function __unserialize(array $data): void {}

    final public function __serialize(): array {}
}
