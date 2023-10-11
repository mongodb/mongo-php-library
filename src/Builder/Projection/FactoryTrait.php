<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Projection;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToBool;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * @internal
 */
trait FactoryTrait
{
    /**
     * Projects the first element in an array that matches the specified $elemMatch condition.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/projection/elemMatch/
     * @param Document|QueryInterface|Serializable|array|stdClass $query
     */
    public static function elemMatch(Document|Serializable|QueryInterface|stdClass|array $query): ElemMatchOperator
    {
        return new ElemMatchOperator($query);
    }

    /**
     * Selects a subset of the array to return an array with only the elements that match the filter condition.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/
     * @param BSONArray|PackedArray|ResolvesToArray|array $input
     * @param ResolvesToBool|bool $cond An expression that resolves to a boolean value used to determine if an element should be included in the output array. The expression references each element of the input array individually with the variable name specified in as.
     * @param Optional|non-empty-string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this.
     * @param Optional|ResolvesToInt|int $limit A number expression that restricts the number of matching array elements that $filter returns. You cannot specify a limit less than 1. The matching array elements are returned in the order they appear in the input array.
     * If the specified limit is greater than the number of matching array elements, $filter returns all matching array elements. If the limit is null, $filter returns all matching array elements.
     */
    public static function filter(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        ResolvesToBool|bool $cond,
        Optional|string $as = Optional::Undefined,
        Optional|ResolvesToInt|int $limit = Optional::Undefined,
    ): FilterOperator
    {
        return new FilterOperator($input, $cond, $as, $limit);
    }

    /**
     * Limits the number of elements projected from an array. Supports skip and limit slices.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/projection/slice/
     * @param int $limit
     * @param int $skip
     */
    public static function slice(int $limit, int $skip): SliceOperator
    {
        return new SliceOperator($limit, $skip);
    }
}
