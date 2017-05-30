<?php

namespace MongoDB;

use MongoDB\Driver\WriteResult;

/**
 * Result class for write operations.
 */
abstract class Result
{
    /**
     * @var bool
     */
    protected $isAcknowledged;

    /**
     * @var WriteResult
     */
    protected $writeResult;

    /**
     * Constructor.
     *
     * @param WriteResult $writeResult
     */
    public function __construct(WriteResult $writeResult)
    {
        $this->writeResult = $writeResult;
        $this->isAcknowledged = $writeResult->isAcknowledged();
    }

    /**
     * Return whether this operation was acknowledged by the server.
     *
     * If the operation was not acknowledged, other fields from the WriteResult
     * (e.g. matchedCount, insertedCount, and deletedCount) will be undefined.
     * Their getter methods should not be invoked in this case.
     *
     * @return boolean
     */
    public function isAcknowledged()
    {
        return $this->isAcknowledged;
    }
}
