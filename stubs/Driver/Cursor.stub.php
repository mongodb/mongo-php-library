<?php

namespace MongoDB\Driver;

use MongoDB\BSON\Int64;

/**
 * @template-covariant TValue of array|object
 *
 * @template-implements CursorInterface<TValue>
 */
final class Cursor implements CursorInterface
{
    /**
     * @return TValue|null
     * @psalm-ignore-nullable-return
     */
    public function current(): array|object|null
    {
    }

    public function next(): void
    {
    }

    /** @psalm-ignore-nullable-return */
    public function key(): ?int
    {
    }

    public function valid(): bool
    {
    }

    public function rewind(): void
    {
    }

    /** @return array<TValue> */
    public function toArray(): array
    {
    }

    public function getId(): Int64
    {
    }

    public function getServer(): Server
    {
    }

    public function isDead(): bool
    {
    }

    public function setTypeMap(array $typemap): void
    {
    }
}
