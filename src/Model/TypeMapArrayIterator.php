<?php
/*
 * Copyright 2016-2017 MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\Model;

use ArrayIterator;
use MongoDB\Exception\BadMethodCallException;

/**
 * Iterator for applying a type map to documents in inline command results.
 *
 * This iterator may be used to apply a type map to an array of documents
 * returned by a database command (e.g. aggregate on servers < 2.6) and allows
 * for functional equivalence with commands that return their results via a
 * cursor (e.g. aggregate on servers >= 2.6).
 *
 * @internal
 */
class TypeMapArrayIterator extends ArrayIterator
{
    private $typeMap;

    /**
     * Constructor.
     *
     * @param array $documents
     * @param array $typeMap
     */
    public function __construct(array $documents = [], array $typeMap)
    {
        parent::__construct($documents);

        $this->typeMap = $typeMap;
    }

    public function append($value)
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    public function asort()
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    /**
     * Return the current element with the type map applied to it.
     *
     * @see http://php.net/arrayiterator.current
     * @return array|object
     */
    public function current()
    {
        return \MongoDB\apply_type_map_to_document(parent::current(), $this->typeMap);
    }

    public function ksort()
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    public function natcasesort()
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    public function natsort()
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    /**
     * Return the value from the provided offset with the type map applied.
     *
     * @see http://php.net/arrayiterator.offsetget
     * @return array|object
     */
    public function offsetGet($offset)
    {
        return \MongoDB\apply_type_map_to_document(parent::offsetGet($offset), $this->typeMap);
    }

    public function offsetSet($index, $newval)
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    public function offsetUnset($index)
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    public function uasort($cmp_function)
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    public function uksort($cmp_function)
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }
}
