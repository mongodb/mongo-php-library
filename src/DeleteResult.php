<?php

namespace MongoDB;

use MongoDB\Driver\WriteResult;

/**
 * Result class for a delete operation.
 */
class DeleteResult
{
    private $writeResult;

    /**
     * Constructor.
     *
     * @param WriteResult $writeResult
     */
    public function __construct(WriteResult $writeResult)
    {
        $this->writeResult = $writeResult;
    }

    /**
     * Return the number of documents that were deleted.
     *
     * This value is undefined if the write was not acknowledged.
     *
     * @see UpdateResult::isAcknowledged()
     * @return integer
     */
    public function getDeletedCount()
    {
        return $this->writeResult->getDeletedCount();
    }

    /**
     * Return whether this delete was acknowledged by the server.
     *
     * If the delete was not acknowledged, other fields from the WriteResult
     * (e.g. deletedCount) will be undefined.
     *
     * @return boolean
     */
    public function isAcknowledged()
    {
        return $this->writeResult->isAcknowledged();
    }
}
