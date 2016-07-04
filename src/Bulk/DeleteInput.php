<?php

namespace MongoDB\Bulk;

use MongoDB\Driver\BulkWrite;
use MongoDB\Exception\InvalidArgumentException;

class DeleteInput implements BulkInputInterface
{
    private $filter;
    private $limit;
    
    public function __construct($filter, $limit)
    {
        if ( ! is_array($filter) && ! is_object($filter)) {
            throw InvalidArgumentException::invalidType('$filter', $filter, 'array or object');
        }

        if ($limit !== 0 && $limit !== 1) {
            throw new InvalidArgumentException('$limit must be 0 or 1');
        }
        
        $this->filter = $filter;
        $this->limit = $limit;
    }
    
    public function addToBulk(BulkWrite $bulk)
    {
        $bulk->delete($this->filter, ['limit' => $this->limit]);
    }
}