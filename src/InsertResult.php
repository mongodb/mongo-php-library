<?php
namespace MongoDB;

class InsertResult {
    protected $wr;

    function __construct(\MongoDB\WriteResult $wr, \BSON\ObjectId $id = null) {
        $this->wr = $wr;
        $this->id = $id;
    }

    function getInsertedId() {
        return $this->id;
    }
}

