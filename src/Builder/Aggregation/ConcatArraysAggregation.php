<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Model\BSONArray;

/**
 * Concatenates arrays to return the concatenated array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/concatArrays/
 */
class ConcatArraysAggregation implements ResolvesToArray
{
    public const NAME = '$concatArrays';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param list<BSONArray|PackedArray|ResolvesToArray|array> ...$array */
    public array $array;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array ...$array
     * @no-named-arguments
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array ...$array)
    {
        if (\count($array) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $array, got %d.', 1, \count($array)));
        }
        if (! \array_is_list($array)) {
            throw new \InvalidArgumentException('Expected $array arguments to be a list (array), named arguments are not supported');
        }
        $this->array = $array;
    }
}
