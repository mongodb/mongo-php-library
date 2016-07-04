<?php

namespace MongoDB\Bulk;

use MongoDB\Driver\BulkWrite;

class DeleteManyInput implements BulkInputInterface
{
    private $deleteInput;
    
    public function __construct($filter)
    {
        $this->deleteInput = new DeleteInput($filter, 0);
    }
    
    public function addToBulk(BulkWrite $bulk)
    {
        $this->deleteInput->addToBulk($bulk);
    }
}