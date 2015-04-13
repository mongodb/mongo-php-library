<?php

namespace MongoDB\Model;

use Iterator;

interface DatabaseInfoIterator extends Iterator
{
    /**
     * Return the current element as a DatabaseInfo instance.
     *
     * @return DatabaseInfo
     */
    public function current();
}
