<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;

/**
 * Returns the element at the specified array index.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayElemAt/
 */
readonly class ArrayElemAtOperator implements ResolvesToAny
{
    public const NAME = '$arrayElemAt';
    public const ENCODE = Encode::Array;

    /** @param BSONArray|PackedArray|ResolvesToArray|array $array */
    public PackedArray|ResolvesToArray|BSONArray|array $array;

    /** @param ResolvesToInt|int $idx */
    public ResolvesToInt|int $idx;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array $array
     * @param ResolvesToInt|int $idx
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array $array, ResolvesToInt|int $idx)
    {
        if (is_array($array) && ! array_is_list($array)) {
            throw new InvalidArgumentException('Expected $array argument to be a list, got an associative array.');
        }

        $this->array = $array;
        $this->idx = $idx;
    }
}
