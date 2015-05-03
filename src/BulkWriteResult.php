<?php

namespace MongoDB;

use BSON\ObjectId;
use MongoDB\Driver\WriteResult;

/**
 * Result class for a bulk write operation.
 */
class BulkWriteResult
{
    private $writeResult;
    private $insertedIds;

    /**
     * Constructor.
     *
     * @param WriteResult $writeResult
     * @param mixed[]     $insertedIds
     */
    public function __construct(WriteResult $writeResult, array $insertedIds)
    {
        $this->writeResult = $writeResult;
        $this->insertedIds = $insertedIds;
    }

    /**
     * Return the number of documents that were deleted.
     *
     * This value is undefined if the write was not acknowledged.
     *
     * @see BulkWriteResult::isAcknowledged()
     * @return integer
     */
    public function getDeletedCount()
    {
        return $this->writeResult->getDeletedCount();
    }

    /**
     * Return the number of documents that were inserted.
     *
     * This value is undefined if the write was not acknowledged.
     *
     * @see BulkWriteResult::isAcknowledged()
     * @return integer
     */
    public function getInsertedCount()
    {
        return $this->writeResult->getInsertedCount();
    }

    /**
     * Return a map of the inserted documents' IDs.
     *
     * The index of each ID in the map corresponds to the document's position
     * in bulk operation. If the document had an ID prior to insertion (i.e. the
     * driver did not generate an ID), this will contain its "_id" field value.
     * Any driver-generated ID will be an MongoDB\Driver\ObjectID instance.
     *
     * @return mixed[]
     */
    public function getInsertedIds()
    {
        return $this->insertedIds;
    }

    /**
     * Return the number of documents that were matched by the filter.
     *
     * This value is undefined if the write was not acknowledged.
     *
     * @see BulkWriteResult::isAcknowledged()
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
     * @see BulkWriteResult::isAcknowledged()
     * @return integer|null
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
     * @see BulkWriteResult::isAcknowledged()
     * @return integer
     */
    public function getUpsertedCount()
    {
        return $this->writeResult->getUpsertedCount();
    }

    /**
     * Return a map of the upserted documents' IDs.
     *
     * The index of each ID in the map corresponds to the document's position
     * in bulk operation. If the document had an ID prior to upserting (i.e. the
     * server did not need to generate an ID), this will contain its "_id". Any
     * server-generated ID will be an MongoDB\Driver\ObjectID instance.
     *
     * @return mixed[]
     */
    public function getUpsertedIds()
    {
        return $this->writeResult->getUpsertedIds();
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
