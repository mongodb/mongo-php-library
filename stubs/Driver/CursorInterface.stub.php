<?php

namespace MongoDB\Driver;

use Iterator;
use MongoDB\BSON\Int64;

/**
 * @template TValue of array|object
 * @template-implements Iterator<int, TValue>
 */
interface CursorInterface extends Iterator
{
    /**
     * @return TValue|null
     * @psalm-ignore-nullable-return
     */
    public function current(): array|object|null;

    public function getId(): Int64;

    public function getServer(): Server;

    public function isDead(): bool;

    /** @psalm-ignore-nullable-return */
    public function key(): ?int;

    public function setTypeMap(array $typemap): void;

    /** @return array<TValue> */
    public function toArray(): array;
}
