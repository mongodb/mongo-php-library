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
 * Converts a date/time string to a date object.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromString/
 */
class DateFromStringOperator implements ResolvesToDate
{
    public const NAME = '$dateFromString';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ResolvesToString|non-empty-string $dateString The date/time string to convert to a date object. */
    public ResolvesToString|string $dateString;

    /**
     * @param Optional|ResolvesToString|non-empty-string $format The date format specification of the dateString. The format can be any expression that evaluates to a string literal, containing 0 or more format specifiers.
     * If unspecified, $dateFromString uses "%Y-%m-%dT%H:%M:%S.%LZ" as the default format but accepts a variety of formats and attempts to parse the dateString if possible.
     */
    public Optional|ResolvesToString|string $format;

    /** @param Optional|ResolvesToString|non-empty-string $timezone The time zone to use to format the date. */
    public Optional|ResolvesToString|string $timezone;

    /**
     * @param Optional|BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onError If $dateFromString encounters an error while parsing the given dateString, it outputs the result value of the provided onError expression. This result value can be of any type.
     * If you do not specify onError, $dateFromString throws an error if it cannot parse dateString.
     */
    public Optional|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onError;

    /**
     * @param Optional|BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onNull If the dateString provided to $dateFromString is null or missing, it outputs the result value of the provided onNull expression. This result value can be of any type.
     * If you do not specify onNull and dateString is null or missing, then $dateFromString outputs null.
     */
    public Optional|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onNull;

    /**
     * @param ResolvesToString|non-empty-string $dateString The date/time string to convert to a date object.
     * @param Optional|ResolvesToString|non-empty-string $format The date format specification of the dateString. The format can be any expression that evaluates to a string literal, containing 0 or more format specifiers.
     * If unspecified, $dateFromString uses "%Y-%m-%dT%H:%M:%S.%LZ" as the default format but accepts a variety of formats and attempts to parse the dateString if possible.
     * @param Optional|ResolvesToString|non-empty-string $timezone The time zone to use to format the date.
     * @param Optional|BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onError If $dateFromString encounters an error while parsing the given dateString, it outputs the result value of the provided onError expression. This result value can be of any type.
     * If you do not specify onError, $dateFromString throws an error if it cannot parse dateString.
     * @param Optional|BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onNull If the dateString provided to $dateFromString is null or missing, it outputs the result value of the provided onNull expression. This result value can be of any type.
     * If you do not specify onNull and dateString is null or missing, then $dateFromString outputs null.
     */
    public function __construct(
        ResolvesToString|string $dateString,
        Optional|ResolvesToString|string $format = Optional::Undefined,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
        Optional|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onError = Optional::Undefined,
        Optional|Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onNull = Optional::Undefined,
    ) {
        $this->dateString = $dateString;
        $this->format = $format;
        $this->timezone = $timezone;
        $this->onError = $onError;
        $this->onNull = $onNull;
    }
}
