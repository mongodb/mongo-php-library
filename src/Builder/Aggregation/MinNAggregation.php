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
 * Returns the n smallest values in an array. Distinct from the $minN accumulator.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minN-array-element/
 */
class MinNAggregation implements ResolvesToArray
{
    public const NAME = '$minN';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param BSONArray|PackedArray|ResolvesToArray|list $input An expression that resolves to the array from which to return the maximal n elements. */
    public PackedArray|ResolvesToArray|BSONArray|array $input;

    /** @param Int64|ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $maxN returns. */
    public Int64|ResolvesToInt|int $n;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list $input An expression that resolves to the array from which to return the maximal n elements.
     * @param Int64|ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $maxN returns.
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array $input, Int64|ResolvesToInt|int $n)
    {
        if (\is_array($input) && ! \array_is_list($input)) {
            throw new \InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }
        $this->input = $input;
        $this->n = $n;
    }
}
