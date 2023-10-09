<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Regex;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Searches an array for an occurrence of a specified value and returns the array index of the first occurrence. Array indexes start at zero.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfArray/
 */
class IndexOfArrayAggregation implements ResolvesToInt
{
    public const NAME = '$indexOfArray';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /**
     * @param ResolvesToString|non-empty-string $array Can be any valid expression as long as it resolves to an array.
     * If the array expression resolves to a value of null or refers to a field that is missing, $indexOfArray returns null.
     * If the array expression does not resolve to an array or null nor refers to a missing field, $indexOfArray returns an error.
     */
    public ResolvesToString|string $array;

    /** @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $search */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $search;

    /**
     * @param Optional|ResolvesToInt|int $start An integer, or a number that can be represented as integers (such as 2.0), that specifies the starting index position for the search. Can be any valid expression that resolves to a non-negative integral number.
     * If unspecified, the starting index position for the search is the beginning of the string.
     */
    public ResolvesToInt|Optional|int $start;

    /**
     * @param Optional|ResolvesToInt|int $end An integer, or a number that can be represented as integers (such as 2.0), that specifies the ending index position for the search. Can be any valid expression that resolves to a non-negative integral number. If you specify a <end> index value, you should also specify a <start> index value; otherwise, $indexOfArray uses the <end> value as the <start> index value instead of the <end> value.
     * If unspecified, the ending index position for the search is the end of the string.
     */
    public ResolvesToInt|Optional|int $end;

    /**
     * @param ResolvesToString|non-empty-string $array Can be any valid expression as long as it resolves to an array.
     * If the array expression resolves to a value of null or refers to a field that is missing, $indexOfArray returns null.
     * If the array expression does not resolve to an array or null nor refers to a missing field, $indexOfArray returns an error.
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $search
     * @param Optional|ResolvesToInt|int $start An integer, or a number that can be represented as integers (such as 2.0), that specifies the starting index position for the search. Can be any valid expression that resolves to a non-negative integral number.
     * If unspecified, the starting index position for the search is the beginning of the string.
     * @param Optional|ResolvesToInt|int $end An integer, or a number that can be represented as integers (such as 2.0), that specifies the ending index position for the search. Can be any valid expression that resolves to a non-negative integral number. If you specify a <end> index value, you should also specify a <start> index value; otherwise, $indexOfArray uses the <end> value as the <start> index value instead of the <end> value.
     * If unspecified, the ending index position for the search is the end of the string.
     */
    public function __construct(
        ResolvesToString|string $array,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $search,
        ResolvesToInt|Optional|int $start = Optional::Undefined,
        ResolvesToInt|Optional|int $end = Optional::Undefined,
    ) {
        $this->array = $array;
        if (\is_array($search) && ! \array_is_list($search)) {
            throw new \InvalidArgumentException('Expected $search argument to be a list, got an associative array.');
        }

        $this->search = $search;
        $this->start = $start;
        $this->end = $end;
    }
}
