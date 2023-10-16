<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\Optional;

/**
 * Truncates a date.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateTrunc/
 */
readonly class DateTruncOperator implements ResolvesToDate
{
    public const NAME = '$dateTrunc';
    public const ENCODE = Encode::Object;

    /** @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to truncate, specified in UTC. The date can be any expression that resolves to a Date, a Timestamp, or an ObjectID. */
    public ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date;

    /**
     * @param ResolvesToString|non-empty-string $unit The unit of time, specified as an expression that must resolve to one of these strings: year, quarter, week, month, day, hour, minute, second.
     * Together, binSize and unit specify the time period used in the $dateTrunc calculation.
     */
    public ResolvesToString|string $unit;

    /**
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $binSize The numeric time value, specified as an expression that must resolve to a positive non-zero number. Defaults to 1.
     * Together, binSize and unit specify the time period used in the $dateTrunc calculation.
     */
    public Optional|Decimal128|Int64|ResolvesToNumber|float|int $binSize;

    /** @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public Optional|ResolvesToString|string $timezone;

    /**
     * @param Optional|non-empty-string $startOfWeek The start of the week. Used when
     * unit is week. Defaults to Sunday.
     */
    public Optional|string $startOfWeek;

    /**
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to truncate, specified in UTC. The date can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|non-empty-string $unit The unit of time, specified as an expression that must resolve to one of these strings: year, quarter, week, month, day, hour, minute, second.
     * Together, binSize and unit specify the time period used in the $dateTrunc calculation.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $binSize The numeric time value, specified as an expression that must resolve to a positive non-zero number. Defaults to 1.
     * Together, binSize and unit specify the time period used in the $dateTrunc calculation.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     * @param Optional|non-empty-string $startOfWeek The start of the week. Used when
     * unit is week. Defaults to Sunday.
     */
    public function __construct(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|string $unit,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $binSize = Optional::Undefined,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
        Optional|string $startOfWeek = Optional::Undefined,
    ) {
        $this->date = $date;
        $this->unit = $unit;
        $this->binSize = $binSize;
        $this->timezone = $timezone;
        $this->startOfWeek = $startOfWeek;
    }
}
