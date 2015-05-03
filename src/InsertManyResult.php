<?php

namespace MongoDB;

use MongoDB\Driver\WriteResult;

/**
 * Result class for a multi-document write operation.
 */
class InsertManyResult
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
     * Return the number of documents that were inserted.
     *
     * This value is undefined if the write was not acknowledged.
     *
     * @see InsertManyResult::isAcknowledged()
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
     * Return whether this insert result was acknowledged by the server.
     *
     * If the insert was not acknowledged, other fields from the WriteResult
     * (e.g. insertedCount) will be undefined.
     *
     * @return boolean
     */
    public function isAcknowledged()
    {
        return $this->writeResult->isAcknowledged();
    }
}
