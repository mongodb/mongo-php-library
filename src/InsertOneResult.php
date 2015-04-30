<?php

namespace MongoDB;

use MongoDB\Driver\WriteResult;

/**
 * Result class for a single-document insert operation.
 */
class InsertOneResult
{
    private $writeResult;
    private $insertedId;

    /**
     * Constructor.
     *
     * @param WriteResult $writeResult
     * @param mixed       $insertedId
     */
    public function __construct(WriteResult $writeResult, $insertedId)
    {
        $this->writeResult = $writeResult;
        $this->insertedId = $insertedId;
    }

    /**
     * Return the number of documents that were inserted.
     *
     * This value is undefined if the write was not acknowledged.
     *
     * @see InsertOneResult::isAcknowledged()
     * @return integer
     */
    public function getInsertedCount()
    {
        return $this->writeResult->getInsertedCount();
    }

    /**
     * Return the inserted document's ID.
     *
     * If the document already an ID prior to insertion (i.e. the driver did not
     * need to generate an ID), this will contain its "_id". Any
     * driver-generated ID will be an MongoDB\Driver\ObjectID instance.
     *
     * @return mixed
     */
    public function getInsertedId()
    {
        return $this->insertedId;
    }

    /**
     * Return whether this insert was acknowledged by the server.
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
