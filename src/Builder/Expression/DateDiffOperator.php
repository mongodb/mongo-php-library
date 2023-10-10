<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;

/**
 * Returns the difference between two dates.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateDiff/
 */
class DateDiffOperator implements ResolvesToInt
{
    public const NAME = '$dateDiff';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $startDate The start of the time period. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID. */
    public ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate;

    /** @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $endDate The end of the time period. The endDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID. */
    public ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $endDate;

    /** @param ResolvesToString|non-empty-string $unit The time measurement unit between the startDate and endDate */
    public ResolvesToString|string $unit;

    /** @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public Optional|ResolvesToString|string $timezone;

    /** @param Optional|ResolvesToString|non-empty-string $startOfWeek Used when the unit is equal to week. Defaults to Sunday. The startOfWeek parameter is an expression that resolves to a case insensitive string */
    public Optional|ResolvesToString|string $startOfWeek;

    /**
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $startDate The start of the time period. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $endDate The end of the time period. The endDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|non-empty-string $unit The time measurement unit between the startDate and endDate
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     * @param Optional|ResolvesToString|non-empty-string $startOfWeek Used when the unit is equal to week. Defaults to Sunday. The startOfWeek parameter is an expression that resolves to a case insensitive string
     */
    public function __construct(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $startDate,
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $endDate,
        ResolvesToString|string $unit,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
        Optional|ResolvesToString|string $startOfWeek = Optional::Undefined,
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->unit = $unit;
        $this->timezone = $timezone;
        $this->startOfWeek = $startOfWeek;
    }
}
