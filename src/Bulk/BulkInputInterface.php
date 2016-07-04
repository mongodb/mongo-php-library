<?php

namespace MongoDB\Bulk;

use MongoDB\Driver\BulkWrite;

interface BulkInputInterface
{
    public function addToBulk(BulkWrite $bulk);
}