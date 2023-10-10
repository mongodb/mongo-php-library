<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Model\BSONArray;

/**
 * Returns the number of elements in the array. Accepts a single expression as argument.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/size/
 */
class SizeOperator implements ResolvesToInt
{
    public const NAME = '$size';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param BSONArray|PackedArray|ResolvesToArray|array $expression The argument for $size can be any expression as long as it resolves to an array. */
    public PackedArray|ResolvesToArray|BSONArray|array $expression;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression The argument for $size can be any expression as long as it resolves to an array.
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array $expression)
    {
        if (\is_array($expression) && ! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression argument to be a list, got an associative array.');
        }

        $this->expression = $expression;
    }
}
