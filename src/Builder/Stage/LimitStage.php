<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Expression\ResolvesToInt;

class LimitStage
{
    public Int64|ResolvesToInt|int $limit;

    /** @param Int64|ResolvesToInt|int $limit */
    public function __construct(Int64|ResolvesToInt|int $limit)
    {
        $this->limit = $limit;
    }
}
