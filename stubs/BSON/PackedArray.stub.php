<?php

namespace MongoDB\BSON;

final class PackedArray implements BSON, \IteratorAggregate, \Serializable
{
    private function __construct() {}

    final static public function fromPHP(array $value): PackedArray {}

    final public function get(int $index): mixed {}

    final public function getIterator(): Iterator {}

    final public function has(int $index): bool {}

    final public function toPHP(?array $typeMap = null): array|object {}

    final public function __toString(): string {}

    final public static function __set_state(array $properties): PackedArray {}

    final public function serialize(): string {}

    /** @param string $serialized */
    final public function unserialize($serialized): void {}

    final public function __unserialize(array $data): void {}

    final public function __serialize(): array {}
}
