<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\Optional;

/**
 * Returns a document containing the constituent parts of a date.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateToParts/
 */
class DateToPartsOperator implements ResolvesToObject
{
    public const NAME = '$dateToParts';
    public const ENCODE = Encode::Object;

    /** @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The input date for which to return parts. date can be any expression that resolves to a Date, a Timestamp, or an ObjectID. */
    public ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date;

    /** @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public Optional|ResolvesToString|string $timezone;

    /** @param Optional|bool $iso8601 If set to true, modifies the output document to use ISO week date fields. Defaults to false. */
    public Optional|bool $iso8601;

    /**
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int $date The input date for which to return parts. date can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     * @param Optional|bool $iso8601 If set to true, modifies the output document to use ISO week date fields. Defaults to false.
     */
    public function __construct(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
        Optional|bool $iso8601 = Optional::Undefined,
    ) {
        $this->date = $date;
        $this->timezone = $timezone;
        $this->iso8601 = $iso8601;
    }
}
