<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;

class SizeQuery implements QueryInterface
{
    public const NAME = '$size';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Int64|int $value */
    public Int64|int $value;

    /**
     * @param Int64|int $value
     */
    public function __construct(Int64|int $value)
    {
        $this->value = $value;
    }
}
