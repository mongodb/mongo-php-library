<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\Decimal128;
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
 * Truncates a date.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateTrunc/
 * @internal
 */
final class DateTruncOperator implements ResolvesToDate, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$dateTrunc';

    public const PROPERTIES = [
        'date' => 'date',
        'unit' => 'unit',
        'binSize' => 'binSize',
        'timezone' => 'timezone',
        'startOfWeek' => 'startOfWeek',
    ];

    /** @var DateTimeInterface|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int|string $date The date to truncate, specified in UTC. The date can be any expression that resolves to a Date, a Timestamp, or an ObjectID. */
    public readonly DateTimeInterface|ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int|string $date;

    /**
     * @var ResolvesToString|TimeUnit|string $unit The unit of time, specified as an expression that must resolve to one of these strings: year, quarter, week, month, day, hour, minute, second.
     * Together, binSize and unit specify the time period used in the $dateTrunc calculation.
     */
    public readonly ResolvesToString|TimeUnit|string $unit;

    /**
     * @var Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $binSize The numeric time value, specified as an expression that must resolve to a positive non-zero number. Defaults to 1.
     * Together, binSize and unit specify the time period used in the $dateTrunc calculation.
     */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $binSize;

    /** @var Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public readonly Optional|ResolvesToString|string $timezone;

    /**
     * @var Optional|string $startOfWeek The start of the week. Used when
     * unit is week. Defaults to Sunday.
     */
    public readonly Optional|string $startOfWeek;

    /**
     * @param DateTimeInterface|ObjectId|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|Timestamp|UTCDateTime|int|string $date The date to truncate, specified in UTC. The date can be any expression that resolves to a Date, a Timestamp, or an ObjectID.
     * @param ResolvesToString|TimeUnit|string $unit The unit of time, specified as an expression that must resolve to one of these strings: year, quarter, week, month, day, hour, minute, second.
     * Together, binSize and unit specify the time period used in the $dateTrunc calculation.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $binSize The numeric time value, specified as an expression that must resolve to a positive non-zero number. Defaults to 1.
     * Together, binSize and unit specify the time period used in the $dateTrunc calculation.
     * @param Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     * @param Optional|string $startOfWeek The start of the week. Used when
     * unit is week. Defaults to Sunday.
     */
    public function __construct(
        DateTimeInterface|ObjectId|Timestamp|UTCDateTime|ResolvesToDate|ResolvesToObjectId|ResolvesToTimestamp|int|string $date,
        ResolvesToString|TimeUnit|string $unit,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $binSize = Optional::Undefined,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
        Optional|string $startOfWeek = Optional::Undefined,
    ) {
        if (is_string($date) && ! str_starts_with($date, '$')) {
            throw new InvalidArgumentException('Argument $date can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->date = $date;
        $this->unit = $unit;
        if (is_string($binSize) && ! str_starts_with($binSize, '$')) {
            throw new InvalidArgumentException('Argument $binSize can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->binSize = $binSize;
        $this->timezone = $timezone;
        $this->startOfWeek = $startOfWeek;
    }
}
