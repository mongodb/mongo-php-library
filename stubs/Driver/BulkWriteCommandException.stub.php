<?php

namespace MongoDB\Driver\Exception;

use MongoDB\Driver\BulkWriteCommandResult;

class BulkWriteCommandException extends ServerException
{
    protected BulkWriteCommandResult $bulkWriteCommandResult;

    final public function getBulkWriteCommandResult(): BulkWriteCommandResult
    {
    }
}
