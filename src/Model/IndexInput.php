<?php

namespace MongoDB\Model;

use BSON\Serializable;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedTypeException;

/**
 * Index input model class.
 *
 * This class is used to validate user input for index creation.
 *
 * @internal
 * @see MongoDB\Collection::createIndexes()
 * @see https://github.com/mongodb/specifications/blob/master/source/enumerate-indexes.rst
 * @see http://docs.mongodb.org/manual/reference/method/db.collection.createIndex/
 */
class IndexInput implements Serializable
{
    private $index;

    /**
    * Constructor.
    *
    * @param array $index Index specification
    */
    public function __construct(array $index)
    {
        if ( ! isset($index['key'])) {
            throw new InvalidArgumentException('Required "key" document is missing from index specification');
        }

        if ( ! is_array($index['key']) && ! is_object($index['key'])) {
            throw new UnexpectedTypeException($index['key'], 'array or object');
        }

        foreach ($index['key'] as $order) {
            if ( ! is_int($order) && ! is_float($order) && ! is_string($order)) {
                throw new UnexpectedTypeException($order, 'numeric or string');
            }
        }

        if ( ! isset($index['ns'])) {
            throw new InvalidArgumentException('Required "ns" option is missing from index specification');
        }

        if ( ! is_string($index['ns'])) {
            throw new UnexpectedTypeException($index['ns'], 'string');
        }

        if ( ! isset($index['name'])) {
            $index['name'] = $this->generateName($index['key']);
        }

        if ( ! is_string($index['name'])) {
            throw new UnexpectedTypeException($index['name'], 'string');
        }

        $this->index = $index;
    }

    /**
     * Return the index name.
     *
     * @param string
     */
    public function __toString()
    {
        return $this->index['name'];
    }

    /**
     * Serialize the index information to BSON for index creation.
     *
     * @see MongoDB\Collection::createIndexes()
     * @see http://php.net/bson-serializable.bsonserialize
     */
    public function bsonSerialize()
    {
        return $this->index;
    }

    /**
     * Generates an index name from its key specification.
     *
     * @param array|object $key Document containing fields mapped to values,
     *                          which denote order or an index type
     * @return string
     */
    private function generateName($key)
    {
        $name = '';

        foreach ($key as $field => $type) {
            $name .= ($name != '' ? '_' : '') . $field . '_' . $type;
        }

        return $name;
    }
}
