<?php

namespace MongoDB\Model;

use Iterator;

interface CollectionInfoIterator extends Iterator
{
    /**
     * Return the current element as a CollectionInfo instance.
     *
     * @return CollectionInfo
     */
    public function current();
}
