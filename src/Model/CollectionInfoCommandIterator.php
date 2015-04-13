<?php

namespace MongoDB\Model;

use IteratorIterator;

class CollectionInfoCommandIterator extends IteratorIterator implements CollectionInfoIterator
{
    /**
     * Return the current element as a CollectionInfo instance.
     *
     * @see CollectionInfoIterator::current()
     * @see http://php.net/iterator.current
     * @return CollectionInfo
     */
    public function current()
    {
        return new CollectionInfo(parent::current());
    }
}
