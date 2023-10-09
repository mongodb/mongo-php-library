<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDate;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToObjectId;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Expression\ResolvesToTimestamp;
use MongoDB\Builder\Optional;

/**
 * Returns the milliseconds of a date as a number between 0 and 999.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/millisecond/
 */
class MillisecondAggregation implements ResolvesToInt
{
    public const NAME = '$millisecond';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID. */
    public ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date;

    /** @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public ResolvesToString|Optional|string $timezone;

    /**
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public function __construct(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ) {
        $this->date = $date;
        $this->timezone = $timezone;
    }
}
