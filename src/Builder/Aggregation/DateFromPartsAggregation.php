<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDate;
use MongoDB\Builder\Expression\ResolvesToDecimal;
use MongoDB\Builder\Expression\ResolvesToDouble;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToLong;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Optional;

/**
 * Constructs a BSON Date object given the date's constituent parts.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromParts/
 */
class DateFromPartsAggregation implements ResolvesToDate
{
    public const NAME = '$dateFromParts';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $year Calendar year. Can be any expression that evaluates to a number. */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $year;

    /** @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $isoWeekYear ISO Week Date Year. Can be any expression that evaluates to a number. */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $isoWeekYear;

    /** @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $month Month. Defaults to 1. */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $month;

    /** @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $isoWeek Week of year. Defaults to 1. */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $isoWeek;

    /** @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $day Day of month. Defaults to 1. */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $day;

    /** @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $isoDayOfWeek Day of week (Monday 1 - Sunday 7). Defaults to 1. */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $isoDayOfWeek;

    /** @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $hour Hour. Defaults to 0. */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $hour;

    /** @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $minute Minute. Defaults to 0. */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $minute;

    /** @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $second Second. Defaults to 0. */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $second;

    /** @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $millisecond Millisecond. Defaults to 0. */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $millisecond;

    /** @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public ResolvesToString|Optional|string $timezone;

    /**
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $year Calendar year. Can be any expression that evaluates to a number.
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $isoWeekYear ISO Week Date Year. Can be any expression that evaluates to a number.
     * @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $month Month. Defaults to 1.
     * @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $isoWeek Week of year. Defaults to 1.
     * @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $day Day of month. Defaults to 1.
     * @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $isoDayOfWeek Day of week (Monday 1 - Sunday 7). Defaults to 1.
     * @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $hour Hour. Defaults to 0.
     * @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $minute Minute. Defaults to 0.
     * @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $second Second. Defaults to 0.
     * @param Decimal128|Int64|Optional|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $millisecond Millisecond. Defaults to 0.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $year,
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $isoWeekYear,
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $month = Optional::Undefined,
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $isoWeek = Optional::Undefined,
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $day = Optional::Undefined,
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $isoDayOfWeek = Optional::Undefined,
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $hour = Optional::Undefined,
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $minute = Optional::Undefined,
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $second = Optional::Undefined,
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|Optional|float|int $millisecond = Optional::Undefined,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
    ) {
        $this->year = $year;
        $this->isoWeekYear = $isoWeekYear;
        $this->month = $month;
        $this->isoWeek = $isoWeek;
        $this->day = $day;
        $this->isoDayOfWeek = $isoDayOfWeek;
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
        $this->millisecond = $millisecond;
        $this->timezone = $timezone;
    }
}
