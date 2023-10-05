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
 * Returns a set with elements that appear in the first set but not in the second set; i.e. performs a relative complement of the second set relative to the first. Accepts exactly two argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setDifference/
 */
class SetDifferenceAggregation implements ResolvesToArray
{
    public const NAME = '$setDifference';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression1 The arguments can be any valid expression as long as they each resolve to an array. */
    public PackedArray|ResolvesToArray|BSONArray|array $expression1;

    /** @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression2 The arguments can be any valid expression as long as they each resolve to an array. */
    public PackedArray|ResolvesToArray|BSONArray|array $expression2;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression1 The arguments can be any valid expression as long as they each resolve to an array.
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression2 The arguments can be any valid expression as long as they each resolve to an array.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array $expression1,
        PackedArray|ResolvesToArray|BSONArray|array $expression2,
    ) {
        if (\is_array($expression1) && ! \array_is_list($expression1)) {
            throw new \InvalidArgumentException('Expected $expression1 argument to be a list, got an associative array.');
        }
        $this->expression1 = $expression1;
        if (\is_array($expression2) && ! \array_is_list($expression2)) {
            throw new \InvalidArgumentException('Expected $expression2 argument to be a list, got an associative array.');
        }
        $this->expression2 = $expression2;
    }
}
