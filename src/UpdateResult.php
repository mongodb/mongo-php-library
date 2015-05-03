<?php

namespace MongoDB;

use MongoDB\Driver\WriteResult;

/**
 * Result class for an update operation.
 */
class UpdateResult
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
     * Return the number of documents that were matched by the filter.
     *
     * This value is undefined if the write was not acknowledged.
     *
     * @see UpdateResult::isAcknowledged()
     * @return integer
     */
    public function getMatchedCount()
    {
        return $this->writeResult->getMatchedCount();
    }

    /**
     * Return the number of documents that were modified.
     *
     * This value is undefined if the write was not acknowledged or if the write
     * executed as a legacy operation instead of write command.
     *
     * @see UpdateResult::isAcknowledged()
     * @return integer
     */
    public function getModifiedCount()
    {
        return $this->writeResult->getModifiedCount();
    }

    /**
     * Return the number of documents that were upserted.
     *
     * This value is undefined if the write was not acknowledged.
     *
     * @see UpdateResult::isAcknowledged()
     * @return integer
     */
    public function getUpsertedCount()
    {
        return $this->writeResult->getUpsertedCount();
    }

    /**
     * Return the ID of the document inserted by an upsert operation.
     *
     * This value is undefined if an upsert did not take place.
     *
     * @return mixed|null
     */
    public function getUpsertedId()
    {
        foreach ($this->writeResult->getUpsertedIds() as $id) {
            return $id;
        }
    }

    /**
     * Return whether this update was acknowledged by the server.
     *
     * If the update was not acknowledged, other fields from the WriteResult
     * (e.g. matchedCount) will be undefined.
     *
     * @return boolean
     */
    public function isAcknowledged()
    {
        return $this->writeResult->isAcknowledged();
    }
}
