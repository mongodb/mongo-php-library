<?php

namespace MongoDB\Bulk;

use MongoDB\Driver\BulkWrite;

class DeleteOneInput implements BulkInputInterface
{
    private $deleteInput;
    
    public function __construct($filter)
    {
        $this->deleteInput = new DeleteInput($filter, 1);
    }

    public function addToBulk(BulkWrite $bulk)
    {
        $this->deleteInput->addToBulk($bulk);
    }
}