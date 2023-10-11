<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Int64;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\Optional;

/**
 * Subtracts a number of time units from a date object.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateSubtract/
 */
class DateSubtractOperator implements ResolvesToDate
{
    public const NAME = '$dateSubtract';
    public const ENCODE = Encode::Object;

    /** @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $startDate The beginning date, in UTC, for the addition operation. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID. */
    public ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate;

    /** @param ResolvesToString|non-empty-string $unit The unit used to measure the amount of time added to the startDate. */
    public ResolvesToString|string $unit;

    /** @param Int64|ResolvesToInt|ResolvesToLong|int $amount */
    public Int64|ResolvesToInt|ResolvesToLong|int $amount;

    /** @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public Optional|ResolvesToString|string $timezone;

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
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ) {
        $this->startDate = $startDate;
        $this->unit = $unit;
        $this->amount = $amount;
        $this->timezone = $timezone;
    }
}
