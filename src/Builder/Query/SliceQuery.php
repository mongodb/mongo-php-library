<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;

/**
 * Limits the number of elements projected from an array. Supports skip and limit slices.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/slice/
 */
class SliceQuery implements QueryInterface
{
    public const NAME = '$slice';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param Int64|int $limit */
    public Int64|int $limit;

    /** @param Int64|int $skip */
    public Int64|int $skip;

    /**
     * @param Int64|int $limit
     * @param Int64|int $skip
     */
    public function __construct(Int64|int $limit, Int64|int $skip)
    {
        $this->limit = $limit;
        $this->skip = $skip;
    }
}
