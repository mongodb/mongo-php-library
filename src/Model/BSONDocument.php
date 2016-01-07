<?php

namespace MongoDB\Model;

use MongoDB\BSON\Serializable;
use MongoDB\BSON\Unserializable;
use ArrayObject;

/**
 * Model class for a BSON document.
 *
 * The internal data will be cast to an object during BSON serialization to
 * ensure that it becomes a BSON document.
 *
 * @api
 */
class BSONDocument extends ArrayObject implements Serializable, Unserializable
{
    /**
     * Serialize the document to BSON.
     *
     * @see http://php.net/mongodb-bson-serializable.bsonserialize
     * @return object
     */
    public function bsonSerialize()
    {
        return (object) $this->getArrayCopy();
    }

    /**
     * Unserialize the document to BSON.
     *
     * @see http://php.net/mongodb-bson-unserializable.bsonunserialize
     * @param array $data Array data
     */
    public function bsonUnserialize(array $data)
    {
        self::__construct($data, ArrayObject::ARRAY_AS_PROPS);
    }
}
