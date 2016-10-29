<?php

namespace MongoDB;

use MongoDB\Driver\WriteResult;
use MongoDB\Exception\BadMethodCallException;

/**
 * Result class for a multi-document insert operation.
 */
class InsertManyResult extends Result
{
    private $insertedIds;

    /**
     * Constructor.
     *
     * @param WriteResult $writeResult
     * @param mixed[]     $insertedIds
     */
    public function __construct(WriteResult $writeResult, array $insertedIds)
    {
        parent::__construct($writeResult);

        $this->insertedIds = $insertedIds;
    }

    /**
     * Return the number of documents that were inserted.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see InsertManyResult::isAcknowledged()
     * @return integer
     * @throws BadMethodCallException is the write result is unacknowledged
     */
    public function getInsertedCount()
    {
        if ($this->isAcknowledged) {
            return $this->writeResult->getInsertedCount();
        }

        throw BadMethodCallException::unacknowledgedWriteResultAccess(__METHOD__);
    }

    /**
     * Return a map of the inserted documents' IDs.
     *
     * The index of each ID in the map corresponds to the document's position in
     * the bulk operation. If the document had an ID prior to insertion (i.e.
     * the driver did not generate an ID), this will contain its "_id" field
     * value. Any driver-generated ID will be an MongoDB\BSON\ObjectID instance.
     *
     * @return mixed[]
     */
    public function getInsertedIds()
    {
        return $this->insertedIds;
    }
}
