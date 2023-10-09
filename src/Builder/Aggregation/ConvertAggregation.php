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
use MongoDB\Builder\Expression\ResolvesToAny;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Converts a value to a specified type.
 * New in version 4.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/convert/
 */
class ConvertAggregation implements ResolvesToAny
{
    public const NAME = '$convert';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $input */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $input;

    /** @param ResolvesToInt|ResolvesToString|int|non-empty-string $to */
    public ResolvesToInt|ResolvesToString|int|string $to;

    /**
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|Optional|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onError The value to return on encountering an error during conversion, including unsupported type conversions. The arguments can be any valid expression.
     * If unspecified, the operation throws an error upon encountering an error and stops.
     */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|Optional|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onError;

    /**
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|Optional|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onNull The value to return if the input is null or missing. The arguments can be any valid expression.
     * If unspecified, $convert returns null if the input is null or missing.
     */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|Optional|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onNull;

    /**
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $input
     * @param ResolvesToInt|ResolvesToString|int|non-empty-string $to
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|Optional|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onError The value to return on encountering an error during conversion, including unsupported type conversions. The arguments can be any valid expression.
     * If unspecified, the operation throws an error upon encountering an error and stops.
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|Optional|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onNull The value to return if the input is null or missing. The arguments can be any valid expression.
     * If unspecified, $convert returns null if the input is null or missing.
     */
    public function __construct(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $input,
        ResolvesToInt|ResolvesToString|int|string $to,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|Optional|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onError = Optional::Undefined,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|Optional|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onNull = Optional::Undefined,
    ) {
        if (\is_array($input) && ! \array_is_list($input)) {
            throw new \InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }

        $this->input = $input;
        $this->to = $to;
        if (\is_array($onError) && ! \array_is_list($onError)) {
            throw new \InvalidArgumentException('Expected $onError argument to be a list, got an associative array.');
        }

        $this->onError = $onError;
        if (\is_array($onNull) && ! \array_is_list($onNull)) {
            throw new \InvalidArgumentException('Expected $onNull argument to be a list, got an associative array.');
        }

        $this->onNull = $onNull;
    }
}
