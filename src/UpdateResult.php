<?php
namespace MongoDB;

class UpdateResult
{
    protected $wr;

    public function __construct(\MongoDB\WriteResult $wr)
    {
        $this->wr = $wr;
    }

    public function getMatchedCount()
    {
        return $this->wr->getMatchedCount();
    }

    public function getModifiedCount()
    {
        return $this->wr->getModifiedCount();
    }

    public function getUpsertedId()
    {
        return $this->wr->getUpsertedIds()[0];
    }
}
