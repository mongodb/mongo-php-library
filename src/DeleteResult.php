<?php

namespace MongoDB;

use MongoDB\Driver\WriteResult;

class DeleteResult
{
    protected $wr;

    public function __construct(WriteResult $wr)
    {
        $this->wr = $wr;
    }

    public function getDeletedCount()
    {
        return $this->wr->getDeletedCount();
    }
}
