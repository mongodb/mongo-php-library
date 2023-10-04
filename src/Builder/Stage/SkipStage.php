<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;

class SkipStage implements StageInterface
{
    public const NAME = '$skip';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Int64|int $skip */
    public Int64|int $skip;

    /**
     * @param Int64|int $skip
     */
    public function __construct(Int64|int $skip)
    {
        $this->skip = $skip;
    }
}
