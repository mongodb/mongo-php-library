<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;

/**
 * Returns the week number for a date as a number between 0 (the partial week that precedes the first Sunday of the year) and 53 (leap year).
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/week/
 * @internal
 */
final class WeekOperator implements ResolvesToInt, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$week';
    public const PROPERTIES = ['date' => 'date', 'timezone' => 'timezone'];

    /** @var ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int|string $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID. */
    public readonly ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int|string $date;

    /** @var Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public readonly Optional|ResolvesToString|string $timezone;

    /**
     * @param ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int|string $date The date to which the operator is applied. date must be a valid expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param Optional|ResolvesToString|string $timezone The timezone of the operation result. timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public function __construct(
        ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int|string $date,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ) {
        $this->date = $date;
        $this->timezone = $timezone;
    }
}
