<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\WindowInterface;
use MongoDB\Model\BSONArray;

/**
 * Returns a specified number of elements from the beginning of an array. Distinct from the $firstN accumulator.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN-array-element/
 */
class FirstNOperator implements AccumulatorInterface, WindowInterface
{
    public const NAME = '$firstN';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to the array from which to return n elements. */
    public PackedArray|ResolvesToArray|BSONArray|array $input;

    /** @param ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $firstN returns. */
    public ResolvesToInt|int $n;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to the array from which to return n elements.
     * @param ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $firstN returns.
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array $input, ResolvesToInt|int $n)
    {
        if (\is_array($input) && ! \array_is_list($input)) {
            throw new \InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }

        $this->input = $input;
        $this->n = $n;
    }
}
