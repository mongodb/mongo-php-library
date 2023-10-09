<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Int64;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDate;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToLong;
use MongoDB\Builder\Expression\ResolvesToObjectId;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Expression\ResolvesToTimestamp;
use MongoDB\Builder\Optional;

/**
 * Adds a number of time units to a date object.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateAdd/
 */
class DateAddAggregation implements ResolvesToDate
{
    public const NAME = '$dateAdd';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $startDate The beginning date, in UTC, for the addition operation. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID. */
    public ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate;

    /** @param ResolvesToString|non-empty-string $unit The unit used to measure the amount of time added to the startDate. */
    public ResolvesToString|string $unit;

    /** @param Int64|ResolvesToInt|ResolvesToLong|int $amount */
    public Int64|ResolvesToInt|ResolvesToLong|int $amount;

    /** @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public ResolvesToString|Optional|string $timezone;

    /**
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $startDate The beginning date, in UTC, for the addition operation. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|non-empty-string $unit The unit used to measure the amount of time added to the startDate.
     * @param Int64|ResolvesToInt|ResolvesToLong|int $amount
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public function __construct(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate,
        ResolvesToString|string $unit,
        Int64|ResolvesToInt|ResolvesToLong|int $amount,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ) {
        $this->startDate = $startDate;
        $this->unit = $unit;
        $this->amount = $amount;
        $this->timezone = $timezone;
    }
}
