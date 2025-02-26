<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\Int64;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\TimeUnit;
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

/**
 * Adds a number of time units to a date object.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateAdd/
 * @internal
 */
final class DateAddOperator implements ResolvesToDate, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$dateAdd';
    public const PROPERTIES = ['startDate' => 'startDate', 'unit' => 'unit', 'amount' => 'amount', 'timezone' => 'timezone'];

    /** @var DateTimeInterface|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int|string $startDate The beginning date, in UTC, for the addition operation. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID. */
    public readonly DateTimeInterface|ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int|string $startDate;

    /** @var ResolvesToString|TimeUnit|string $unit The unit used to measure the amount of time added to the startDate. */
    public readonly ResolvesToString|TimeUnit|string $unit;

    /** @var Int64|ResolvesToInt|ResolvesToLong|int|string $amount */
    public readonly Int64|ResolvesToInt|ResolvesToLong|int|string $amount;

    /** @var Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public readonly Optional|ResolvesToString|string $timezone;

    /**
     * @param DateTimeInterface|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int|string $startDate The beginning date, in UTC, for the addition operation. The startDate can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|TimeUnit|string $unit The unit used to measure the amount of time added to the startDate.
     * @param Int64|ResolvesToInt|ResolvesToLong|int|string $amount
     * @param Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public function __construct(
        DateTimeInterface|ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int|string $startDate,
        ResolvesToString|TimeUnit|string $unit,
        Int64|ResolvesToInt|ResolvesToLong|int|string $amount,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
    ) {
        if (is_string($startDate) && ! str_starts_with($startDate, '$')) {
            throw new InvalidArgumentException('Argument $startDate can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->startDate = $startDate;
        $this->unit = $unit;
        if (is_string($amount) && ! str_starts_with($amount, '$')) {
            throw new InvalidArgumentException('Argument $amount can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->amount = $amount;
        $this->timezone = $timezone;
    }
}
