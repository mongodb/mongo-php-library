<?php

namespace MongoDB\BSON;

/**
 * This stub file is temporary and can be removed when using Psalm 5
 *
 * @template TValue
 * @template-implements \IteratorAggregate<string, TValue>
 */
final class Document implements \IteratorAggregate, \Serializable
{
    private function __construct() {}

    final static public function fromBSON(string $bson): Document {}

    final static public function fromJSON(string $json): Document {}

    /** @param array|object $value */
    final static public function fromPHP($value): Document {}

    /** @return TValue */
    final public function get(string $key) {}

    /** @return Iterator<string, TValue> */
    final public function getIterator(): Iterator {}

    final public function has(string $key): bool {}

    /** @return array|object */
    final public function toPHP(?array $typeMap = null) {}

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
