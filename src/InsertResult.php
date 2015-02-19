<?php

namespace MongoDB;

use BSON\ObjectId;
use MongoDB\Driver\WriteResult;

class InsertResult
{
    protected $wr;

    public function __construct(WriteResult $wr, ObjectId $id = null)
    {
        $this->wr = $wr;
        $this->id = $id;
    }

    public function getInsertedId()
    {
        return $this->id;
    }
}
