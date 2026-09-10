<?php

namespace MongoDB\Driver;

use Countable;

final class BulkWriteCommand implements Countable
{
    final public function __construct(?array $options = null)
    {
    }

    public function count(): int
    {
    }

    final public function deleteOne(string $namespace, array|object $filter, ?array $options = null): void
    {
    }

    final public function deleteMany(string $namespace, array|object $filter, ?array $options = null): void
    {
    }

    final public function insertOne(string $namespace, array|object $document): mixed
    {
    }

    final public function replaceOne(string $namespace, array|object $filter, array|object $replacement, ?array $options = null): void
    {
    }

    final public function updateOne(string $namespace, array|object $filter, array|object $update, ?array $options = null): void
    {
    }

    final public function updateMany(string $namespace, array|object $filter, array|object $update, ?array $options = null): void
    {
    }
}
