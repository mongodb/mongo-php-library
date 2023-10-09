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
use MongoDB\Builder\Expression\ResolvesToDate;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToObjectId;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Expression\ResolvesToTimestamp;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Returns the date as a formatted string.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateToString/
 */
class DateToStringAggregation implements ResolvesToString
{
    public const NAME = '$dateToString';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to convert to string. Must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID. */
    public ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date;

    /**
     * @param Optional|ResolvesToString|non-empty-string $format The date format specification of the dateString. The format can be any expression that evaluates to a string literal, containing 0 or more format specifiers.
     * If unspecified, $dateFromString uses "%Y-%m-%dT%H:%M:%S.%LZ" as the default format but accepts a variety of formats and attempts to parse the dateString if possible.
     */
    public ResolvesToString|Optional|string $format;

    /** @param Optional|ResolvesToString|non-empty-string $timezone The time zone to use to format the date. */
    public ResolvesToString|Optional|string $timezone;

    /**
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|Optional|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onNull The value to return if the date is null or missing.
     * If unspecified, $dateToString returns null if the date is null or missing.
     */
    public Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|Optional|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onNull;

    /**
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to convert to string. Must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $format The date format specification of the dateString. The format can be any expression that evaluates to a string literal, containing 0 or more format specifiers.
     * If unspecified, $dateFromString uses "%Y-%m-%dT%H:%M:%S.%LZ" as the default format but accepts a variety of formats and attempts to parse the dateString if possible.
     * @param Optional|ResolvesToString|non-empty-string $timezone The time zone to use to format the date.
     * @param BSONArray|Binary|Decimal128|Document|ExpressionInterface|Int64|ObjectId|Optional|PackedArray|Regex|ResolvesToInt|Serializable|Timestamp|UTCDateTime|array|bool|float|int|non-empty-string|null|stdClass $onNull The value to return if the date is null or missing.
     * If unspecified, $dateToString returns null if the date is null or missing.
     */
    public function __construct(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $format = Optional::Undefined,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
        Binary|Decimal128|Document|Int64|ObjectId|PackedArray|Regex|Serializable|Timestamp|UTCDateTime|ResolvesToInt|Optional|ExpressionInterface|BSONArray|stdClass|array|bool|float|int|null|string $onNull = Optional::Undefined,
    ) {
        $this->date = $date;
        $this->format = $format;
        $this->timezone = $timezone;
        if (\is_array($onNull) && ! \array_is_list($onNull)) {
            throw new \InvalidArgumentException('Expected $onNull argument to be a list, got an associative array.');
        }

        $this->onNull = $onNull;
    }
}
