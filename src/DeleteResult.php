<?php
namespace MongoDB;

class DeleteResult
{
    protected $wr;

    public function __construct(\MongoDB\WriteResult $wr)
    {
        $this->wr = $wr;
    }

    public function getDeletedCount()
    {
        return $this->wr->getDeletedCount();
    }
}
