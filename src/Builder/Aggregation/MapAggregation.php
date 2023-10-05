<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Optional;
use MongoDB\Model\BSONArray;

/**
 * Applies a subexpression to each element of an array and returns the array of resulting values in order. Accepts named parameters.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/map/
 */
class MapAggregation implements ResolvesToArray
{
    public const NAME = '$map';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $input An expression that resolves to an array. */
    public PackedArray|ResolvesToArray|BSONArray|array $input;

    /** @param ExpressionInterface|mixed $in An expression that is applied to each element of the input array. The expression references each element individually with the variable name specified in as. */
    public mixed $in;

    /** @param Optional|ResolvesToString|non-empty-string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this. */
    public ResolvesToString|Optional|string $as;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $input An expression that resolves to an array.
     * @param ExpressionInterface|mixed $in An expression that is applied to each element of the input array. The expression references each element individually with the variable name specified in as.
     * @param Optional|ResolvesToString|non-empty-string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        mixed $in,
        ResolvesToString|Optional|string $as = Optional::Undefined,
    ) {
        if (\is_array($input) && ! \array_is_list($input)) {
            throw new \InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }
        $this->input = $input;
        $this->in = $in;
        $this->as = $as;
    }
}
