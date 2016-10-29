<?php

namespace MongoDB;

use MongoDB\Exception\BadMethodCallException;

/**
 * Result class for a delete operation.
 */
class DeleteResult extends Result
{
    /**
     * Return the number of documents that were deleted.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see DeleteResult::isAcknowledged()
     * @return integer
     * @throws BadMethodCallException is the write result is unacknowledged
     */
    public function getDeletedCount()
    {
        if ($this->isAcknowledged) {
            return $this->writeResult->getDeletedCount();
        }

        throw BadMethodCallException::unacknowledgedWriteResultAccess(__METHOD__);
    }
}
