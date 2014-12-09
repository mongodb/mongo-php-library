<?php
namespace MongoDB;

class UpdateResult {
    protected $wr;

    function __construct(\MongoDB\WriteResult $wr) {
        $this->wr = $wr;
    }

    function getMatchedCount() {
        return $this->wr->getMatchedCount();
    }

    function getModifiedCount() {
        return $this->wr->getModifiedCount();
    }

    function getUpsertedId() {
        return $this->wr->getUpsertedIds()[0];
    }
}



