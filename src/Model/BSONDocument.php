<?php

namespace MongoDB\Model;

use MongoDB\BSON\Serializable;
use MongoDB\BSON\Unserializable;
use ArrayAccess;

/**
 * Model class for a BSON document.
 *
 * The internal data will be cast to an object during BSON serialization to
 * ensure that it becomes a BSON document.
 *
 * @api
 */
class BSONDocument implements ArrayAccess, Serializable, Unserializable
{
    private $data;

    /**
     * Constructor.
     *
     * @param array|object $data Document data
     * @throws InvalidArgumentException
     */
    public function __construct($data)
    {
        if ( ! is_array($data) && ! is_object($data)) {
            throw new InvalidArgumentTypeException('$data', $data, 'array or object');
        }

        $this->data = (array) $document;
    }

    /**
     * Return the document data.
     *
     * @see http://php.net/oop5.magic#language.oop5.magic.debuginfo
     * @return array
     */
    public function __debugInfo()
    {
        return $this->data;
    }

    /**
     * Serialize the document to BSON.
     *
     * @see http://php.net/mongodb-bson-serializable.bsonserialize
     * @return object
     */
    public function bsonSerialize()
    {
        return (object) $this->data;
    }

    /**
     * Unserialize the document to BSON.
     *
     * @see http://php.net/mongodb-bson-unserializable.bsonunserialize
     * @param array $data Document data
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
