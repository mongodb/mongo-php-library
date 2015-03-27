<?php

namespace MongoDB\Model;

use FilterIterator;

class CollectionInfoLegacyIterator extends FilterIterator implements CollectionInfoIterator
{
    /**
     * Return the current element as a CollectionInfo instance.
     *
     * @return CollectionInfo
     */
    public function current()
    {
        $info = parent::current();

        // Trim the database prefix up to and including the first dot
        $firstDot = strpos($info['name'], '.');

        if ($firstDot !== false) {
            $info['name'] = (string) substr($info['name'], $firstDot + 1);
        }

        return new CollectionInfo($info);
    }

    /**
     * Filter out internal or invalid collections.
     *
     * @see http://php.net/manual/en/filteriterator.accept.php
     * @return boolean
     */
    public function accept()
    {
        $info = parent::current();

        if ( ! isset($info['name']) || ! is_string($info['name'])) {
            return false;
        }

        // Reject names with "$" characters (e.g. indexes, oplog)
        if (strpos($info['name'], '$') !== false) {
            return false;
        }

        $firstDot = strpos($info['name'], '.');

        /* Legacy collection names are a namespace and should be prefixed with
         * the database name and a dot. Reject values that omit this prefix or
         * are empty beyond it.
         */
        if ($firstDot === false || $firstDot + 1 == strlen($info['name'])) {
            return false;
        }

        return true;
    }
}
