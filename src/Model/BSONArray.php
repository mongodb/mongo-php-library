<?php

namespace MongoDB\Model;

use MongoDB\BSON\Serializable;
use MongoDB\BSON\Unserializable;
use ArrayAccess;

/**
 * Model class for a BSON array.
 *
 * The internal data will be filtered through array_values() during BSON
 * serialization to ensure that it becomes a BSON array.
 *
 * @api
 */
class BSONArray implements ArrayAccess, Serializable, Unserializable
{
    private $data;

    /**
     * Constructor.
     *
     * @param array $data Array data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Return the array data.
     *
     * @see http://php.net/oop5.magic#language.oop5.magic.debuginfo
     * @return array
     */
    public function __debugInfo()
    {
        return $this->data;
    }

    /**
     * Serialize the array to BSON.
     *
     * The array data will be numerically reindexed to ensure that it is stored
     * as a BSON array.
     *
     * @see http://php.net/mongodb-bson-serializable.bsonserialize
     * @return array
     */
    public function bsonSerialize()
    {
        return array_values($this->data);
    }

    /**
     * Unserialize the document to BSON.
     *
     * @see http://php.net/mongodb-bson-unserializable.bsonunserialize
     * @param array $data Array data
     */
    public function bsonUnserialize(array $data)
    {
        $this->data = $data;
    }

    /**
     * Check whether a field exists.
     *
     * @see http://php.net/arrayaccess.offsetexists
     * @param mixed $key Field name
     * @return boolean
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Return the field's value.
     *
     * @see http://php.net/arrayaccess.offsetget
     * @param mixed $key Field name
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->data[$key];
    }

    /**
     * Set the field's value.
     *
     * @see http://php.net/arrayaccess.offsetset
     * @param mixed $key   Field name
     * @param mixed $value Field value
     */
    public function offsetSet($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Unset the field.
     *
     * @see http://php.net/arrayaccess.offsetunset
     * @param mixed $key   Field name
     */
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }
}
