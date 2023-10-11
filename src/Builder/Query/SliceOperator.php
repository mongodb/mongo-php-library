<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\ProjectionInterface;

/**
 * Limits the number of elements projected from an array. Supports skip and limit slices.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/projection/slice/
 */
class SliceOperator implements ProjectionInterface
{
    public const NAME = '$slice';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param int $limit */
    public int $limit;

    /** @param int $skip */
    public int $skip;

    /**
     * @param int $limit
     * @param int $skip
     */
    public function __construct(int $limit, int $skip)
    {
        $this->limit = $limit;
        $this->skip = $skip;
    }
}
