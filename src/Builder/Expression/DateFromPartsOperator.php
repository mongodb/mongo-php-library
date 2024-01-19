<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;

/**
 * Constructs a BSON Date object given the date's constituent parts.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromParts/
 */
class DateFromPartsOperator implements ResolvesToDate, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int $year Calendar year. Can be any expression that evaluates to a number. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int $year;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoWeekYear ISO Week Date Year. Can be any expression that evaluates to a number. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoWeekYear;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int $month Month. Defaults to 1. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int $month;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoWeek Week of year. Defaults to 1. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoWeek;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int $day Day of month. Defaults to 1. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int $day;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoDayOfWeek Day of week (Monday 1 - Sunday 7). Defaults to 1. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoDayOfWeek;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int $hour Hour. Defaults to 0. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int $hour;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int $minute Minute. Defaults to 0. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int $minute;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int $second Second. Defaults to 0. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int $second;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int $millisecond Millisecond. Defaults to 0. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int $millisecond;

    /** @var Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public readonly Optional|ResolvesToString|string $timezone;

    /**
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $year Calendar year. Can be any expression that evaluates to a number.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoWeekYear ISO Week Date Year. Can be any expression that evaluates to a number.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $month Month. Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoWeek Week of year. Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $day Day of month. Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoDayOfWeek Day of week (Monday 1 - Sunday 7). Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $hour Hour. Defaults to 0.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $minute Minute. Defaults to 0.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $second Second. Defaults to 0.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int $millisecond Millisecond. Defaults to 0.
     * @param Optional|ResolvesToString|non-empty-string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public function __construct(
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $year = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoWeekYear = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $month = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoWeek = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $day = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $isoDayOfWeek = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $hour = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $minute = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $second = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int $millisecond = Optional::Undefined,
        Optional|ResolvesToString|string $timezone = Optional::Undefined,
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

    public function getOperator(): string
    {
        return '$dateFromParts';
    }
}
