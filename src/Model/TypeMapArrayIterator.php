<?php

namespace MongoDB\Model;

use ArrayIterator;

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
}
