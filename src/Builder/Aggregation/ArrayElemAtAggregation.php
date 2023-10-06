<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Int64;
use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Model\BSONArray;

/**
 * Returns the element at the specified array index.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayElemAt/
 */
class ArrayElemAtAggregation implements ExpressionInterface
{
    public const NAME = '$arrayElemAt';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param BSONArray|PackedArray|ResolvesToArray|list $array */
    public PackedArray|ResolvesToArray|BSONArray|array $array;

    /** @param Int64|ResolvesToInt|int $idx */
    public Int64|ResolvesToInt|int $idx;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list $array
     * @param Int64|ResolvesToInt|int $idx
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array $array, Int64|ResolvesToInt|int $idx)
    {
        if (\is_array($array) && ! \array_is_list($array)) {
            throw new \InvalidArgumentException('Expected $array argument to be a list, got an associative array.');
        }
        $this->array = $array;
        $this->idx = $idx;
    }
}
