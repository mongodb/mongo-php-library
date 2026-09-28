<?php

namespace MongoDB\Driver\Exception;

use MongoDB\BSON\Document;
use MongoDB\Driver\BulkWriteCommandResult;

final class BulkWriteCommandException extends ServerException
{
    private ?Document $errorReply = null;

    private ?BulkWriteCommandResult $partialResult = null;

    private array $writeErrors = [];

    private array $writeConcernErrors = [];

    final public function getErrorReply(): ?Document
    {
    }

    final public function getPartialResult(): ?BulkWriteCommandResult
    {
    }

    final public function getWriteErrors(): array
    {
    }

    final public function getWriteConcernErrors(): array
    {
    }
}
