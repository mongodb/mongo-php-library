<?php

namespace MongoDB\Driver;

use MongoDB\BSON\Document;

final class BulkWriteCommandResult
{
    final private function __construct()
    {
    }

    final public function getInsertedCount(): int
    {
    }

    final public function getMatchedCount(): int
    {
    }

    final public function getModifiedCount(): int
    {
    }

    final public function getUpsertedCount(): int
    {
    }

    final public function getDeletedCount(): int
    {
    }

    final public function getInsertResults(): ?Document
    {
    }

    final public function getUpdateResults(): ?Document
    {
    }

    final public function getDeleteResults(): ?Document
    {
    }

    final public function isAcknowledged(): bool
    {
    }
}
