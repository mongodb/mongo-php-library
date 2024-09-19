<?php

namespace MongoDB\Driver;

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
}
