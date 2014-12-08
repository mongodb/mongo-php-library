<?php
namespace MongoDB;

class DeleteResult {
    protected $wr;

    function __construct(\MongoDB\WriteResult $wr) {
        $this->wr = $wr;
    }

    function getDeletedCount() {
        return $this->wr->getDeletedCount();
    }
}


