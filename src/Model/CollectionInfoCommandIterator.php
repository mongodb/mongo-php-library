<?php

namespace MongoDB\Model;

use IteratorIterator;

class CollectionInfoCommandIterator extends IteratorIterator implements CollectionInfoIterator
{
    /**
     * Return the current element as a CollectionInfo instance.
     *
     * @return CollectionInfo
     */
    public function current()
    {
        return new CollectionInfo(parent::current());
    }
}
