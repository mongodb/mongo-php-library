<?php

namespace MongoDB\BSON;

/**
 * This stub file is temporary and can be removed when using Psalm 5
 *
 * @template TValue
 * @template-implements \IteratorAggregate<int, TValue>
 */
final class PackedArray implements \IteratorAggregate, \Serializable
{
    private function __construct() {}

    final static public function fromPHP(array $value): PackedArray {}

    /** @return TValue */
    final public function get(int $index) {}

    /** @return Iterator<int, TValue> */
    final public function getIterator(): Iterator {}

    final public function has(int $index): bool {}

    /** @return array|object */
    final public function toPHP(?array $typeMap = null) {}

    final public function __toString(): string {}

    final public static function __set_state(array $properties): PackedArray {}

    final public function serialize(): string {}

    /** @param string $serialized */
    final public function unserialize($serialized): void {}

    final public function __unserialize(array $data): void {}

    final public function __serialize(): array {}
}
