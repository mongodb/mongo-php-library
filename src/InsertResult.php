<?php
namespace MongoDB;

class InsertResult
{
    protected $wr;

    public function __construct(\MongoDB\WriteResult $wr, \BSON\ObjectId $id = null)
    {
        $this->wr = $wr;
        $this->id = $id;
    }

    public function getInsertedId()
    {
        return $this->id;
    }
}
