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
use MongoDB\Builder\Expression\ResolvesToBool;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Optional;
use MongoDB\Model\BSONArray;

/**
 * Selects a subset of the array to return an array with only the elements that match the filter condition.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/
 */
class FilterAggregation implements ResolvesToArray
{
    public const NAME = '$filter';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $input */
    public PackedArray|ResolvesToArray|BSONArray|array $input;

    /** @param ResolvesToBool|bool $cond An expression that resolves to a boolean value used to determine if an element should be included in the output array. The expression references each element of the input array individually with the variable name specified in as. */
    public ResolvesToBool|bool $cond;

    /** @param Optional|non-empty-string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this. */
    public Optional|string $as;

    /**
     * @param Int64|Optional|ResolvesToInt|int $limit A number expression that restricts the number of matching array elements that $filter returns. You cannot specify a limit less than 1. The matching array elements are returned in the order they appear in the input array.
     * If the specified limit is greater than the number of matching array elements, $filter returns all matching array elements. If the limit is null, $filter returns all matching array elements.
     */
    public Int64|ResolvesToInt|Optional|int $limit;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $input
     * @param ResolvesToBool|bool $cond An expression that resolves to a boolean value used to determine if an element should be included in the output array. The expression references each element of the input array individually with the variable name specified in as.
     * @param Optional|non-empty-string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this.
     * @param Int64|Optional|ResolvesToInt|int $limit A number expression that restricts the number of matching array elements that $filter returns. You cannot specify a limit less than 1. The matching array elements are returned in the order they appear in the input array.
     * If the specified limit is greater than the number of matching array elements, $filter returns all matching array elements. If the limit is null, $filter returns all matching array elements.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        ResolvesToBool|bool $cond,
        Optional|string $as = Optional::Undefined,
        Int64|ResolvesToInt|Optional|int $limit = Optional::Undefined,
    ) {
        if (\is_array($input) && ! \array_is_list($input)) {
            throw new \InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }
        $this->input = $input;
        $this->cond = $cond;
        $this->as = $as;
        $this->limit = $limit;
    }
}