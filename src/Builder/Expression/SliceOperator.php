<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Type\ProjectionInterface;
use MongoDB\Model\BSONArray;

/**
 * Returns a subset of an array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/slice/
 */
class SliceOperator implements ResolvesToArray, ProjectionInterface
{
    public const NAME = '$slice';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param BSONArray|PackedArray|ResolvesToArray|array $expression Any valid expression as long as it resolves to an array. */
    public PackedArray|ResolvesToArray|BSONArray|array $expression;

    /**
     * @param ResolvesToInt|int $n Any valid expression as long as it resolves to an integer. If position is specified, n must resolve to a positive integer.
     * If positive, $slice returns up to the first n elements in the array. If the position is specified, $slice returns the first n elements starting from the position.
     * If negative, $slice returns up to the last n elements in the array. n cannot resolve to a negative number if <position> is specified.
     */
    public ResolvesToInt|int $n;

    /**
     * @param Optional|ResolvesToInt|int $position Any valid expression as long as it resolves to an integer.
     * If positive, $slice determines the starting position from the start of the array. If position is greater than the number of elements, the $slice returns an empty array.
     * If negative, $slice determines the starting position from the end of the array. If the absolute value of the <position> is greater than the number of elements, the starting position is the start of the array.
     */
    public Optional|ResolvesToInt|int $position;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array $expression Any valid expression as long as it resolves to an array.
     * @param ResolvesToInt|int $n Any valid expression as long as it resolves to an integer. If position is specified, n must resolve to a positive integer.
     * If positive, $slice returns up to the first n elements in the array. If the position is specified, $slice returns the first n elements starting from the position.
     * If negative, $slice returns up to the last n elements in the array. n cannot resolve to a negative number if <position> is specified.
     * @param Optional|ResolvesToInt|int $position Any valid expression as long as it resolves to an integer.
     * If positive, $slice determines the starting position from the start of the array. If position is greater than the number of elements, the $slice returns an empty array.
     * If negative, $slice determines the starting position from the end of the array. If the absolute value of the <position> is greater than the number of elements, the starting position is the start of the array.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array $expression,
        ResolvesToInt|int $n,
        Optional|ResolvesToInt|int $position = Optional::Undefined,
    ) {
        if (\is_array($expression) && ! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression argument to be a list, got an associative array.');
        }

        $this->expression = $expression;
        $this->n = $n;
        $this->position = $position;
    }
}
