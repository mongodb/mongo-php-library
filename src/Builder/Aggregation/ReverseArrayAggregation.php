<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Model\BSONArray;

/**
 * Returns an array with the elements in reverse order.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reverseArray/
 */
class ReverseArrayAggregation implements ResolvesToArray
{
    public const NAME = '$reverseArray';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression The argument can be any valid expression as long as it resolves to an array. */
    public PackedArray|ResolvesToArray|BSONArray|array $expression;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression The argument can be any valid expression as long as it resolves to an array.
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array $expression)
    {
        if (\is_array($expression) && ! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression argument to be a list, got an associative array.');
        }
        $this->expression = $expression;
    }
}
