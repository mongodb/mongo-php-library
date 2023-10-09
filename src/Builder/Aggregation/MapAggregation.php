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
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Applies a subexpression to each element of an array and returns the array of resulting values in order. Accepts named parameters.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/map/
 */
class MapAggregation implements ResolvesToArray
{
    public const NAME = '$map';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to an array. */
    public PackedArray|ResolvesToArray|BSONArray|array $input;

    /** @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $in An expression that is applied to each element of the input array. The expression references each element individually with the variable name specified in as. */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $in;

    /** @param Optional|ResolvesToString|non-empty-string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this. */
    public ResolvesToString|Optional|string $as;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to an array.
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $in An expression that is applied to each element of the input array. The expression references each element individually with the variable name specified in as.
     * @param Optional|ResolvesToString|non-empty-string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $in,
        ResolvesToString|Optional|string $as = Optional::Undefined,
    ) {
        if (\is_array($input) && ! \array_is_list($input)) {
            throw new \InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }

        $this->input = $input;
        if (\is_array($in) && ! \array_is_list($in)) {
            throw new \InvalidArgumentException('Expected $in argument to be a list, got an associative array.');
        }

        $this->in = $in;
        $this->as = $as;
    }
}
