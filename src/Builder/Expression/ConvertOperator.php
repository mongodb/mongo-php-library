<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

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
class ConvertOperator implements ResolvesToAny
{
    public const NAME = '$convert';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $input */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $input;

    /** @param ResolvesToInt|ResolvesToString|int|non-empty-string $to */
    public ResolvesToInt|ResolvesToString|int|string $to;

    /**
     * @param Optional|BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onError The value to return on encountering an error during conversion, including unsupported type conversions. The arguments can be any valid expression.
     * If unspecified, the operation throws an error upon encountering an error and stops.
     */
    public Optional|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onError;

    /**
     * @param Optional|BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onNull The value to return if the input is null or missing. The arguments can be any valid expression.
     * If unspecified, $convert returns null if the input is null or missing.
     */
    public Optional|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onNull;

    /**
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $input
     * @param ResolvesToInt|ResolvesToString|int|non-empty-string $to
     * @param Optional|BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onError The value to return on encountering an error during conversion, including unsupported type conversions. The arguments can be any valid expression.
     * If unspecified, the operation throws an error upon encountering an error and stops.
     * @param Optional|BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onNull The value to return if the input is null or missing. The arguments can be any valid expression.
     * If unspecified, $convert returns null if the input is null or missing.
     */
    public function __construct(
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $input,
        ResolvesToInt|ResolvesToString|int|string $to,
        Optional|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onError = Optional::Undefined,
        Optional|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onNull = Optional::Undefined,
    ) {
        $this->input = $input;
        $this->to = $to;
        $this->onError = $onError;
        $this->onNull = $onNull;
    }
}
