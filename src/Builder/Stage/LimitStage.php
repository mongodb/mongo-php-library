<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Int64;

class LimitStage implements StageInterface
{
    public const NAME = '$limit';
    public const ENCODE = 'single';

    public Int64|int $limit;

    /**
     * @param Int64|int $limit
     */
    public function __construct(Int64|int $limit)
    {
        $this->limit = $limit;
    }
}
