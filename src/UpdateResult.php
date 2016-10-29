<?php

namespace MongoDB;

use MongoDB\Exception\BadMethodCallException;

/**
 * Result class for an update operation.
 */
class UpdateResult extends Result
{
    /**
     * Return the number of documents that were matched by the filter.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see UpdateResult::isAcknowledged()
     * @return integer
     * @throws BadMethodCallException is the write result is unacknowledged
     */
    public function getMatchedCount()
    {
        if ($this->isAcknowledged) {
            return $this->writeResult->getMatchedCount();
        }

        throw BadMethodCallException::unacknowledgedWriteResultAccess(__METHOD__);
    }

    /**
     * Return the number of documents that were modified.
     *
     * This value is undefined (i.e. null) if the write executed as a legacy
     * operation instead of command.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see UpdateResult::isAcknowledged()
     * @return integer|null
     * @throws BadMethodCallException is the write result is unacknowledged
     */
    public function getModifiedCount()
    {
        if ($this->isAcknowledged) {
            return $this->writeResult->getModifiedCount();
        }

        throw BadMethodCallException::unacknowledgedWriteResultAccess(__METHOD__);
    }

    /**
     * Return the number of documents that were upserted.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see UpdateResult::isAcknowledged()
     * @return integer
     * @throws BadMethodCallException is the write result is unacknowledged
     */
    public function getUpsertedCount()
    {
        if ($this->isAcknowledged) {
            return $this->writeResult->getUpsertedCount();
        }

        throw BadMethodCallException::unacknowledgedWriteResultAccess(__METHOD__);
    }

    /**
     * Return the ID of the document inserted by an upsert operation.
     *
     * This value is undefined (i.e. null) if an upsert did not take place.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see UpdateResult::isAcknowledged()
     * @return mixed|null
     * @throws BadMethodCallException is the write result is unacknowledged
     */
    public function getUpsertedId()
    {
        if ($this->isAcknowledged) {
            foreach ($this->writeResult->getUpsertedIds() as $id) {
                return $id;
            }

            return null;
        }

        throw BadMethodCallException::unacknowledgedWriteResultAccess(__METHOD__);
    }
}
