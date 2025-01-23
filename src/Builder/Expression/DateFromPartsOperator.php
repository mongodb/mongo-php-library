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
 * @internal
 */
final class DateFromPartsOperator implements ResolvesToDate, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$dateFromParts';

    public const PROPERTIES = [
        'year' => 'year',
        'isoWeekYear' => 'isoWeekYear',
        'month' => 'month',
        'isoWeek' => 'isoWeek',
        'day' => 'day',
        'isoDayOfWeek' => 'isoDayOfWeek',
        'hour' => 'hour',
        'minute' => 'minute',
        'second' => 'second',
        'millisecond' => 'millisecond',
        'timezone' => 'timezone',
    ];

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $year Calendar year. Can be any expression that evaluates to a number. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $year;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $isoWeekYear ISO Week Date Year. Can be any expression that evaluates to a number. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $isoWeekYear;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $month Month. Defaults to 1. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $month;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $isoWeek Week of year. Defaults to 1. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $isoWeek;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $day Day of month. Defaults to 1. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $day;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $isoDayOfWeek Day of week (Monday 1 - Sunday 7). Defaults to 1. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $isoDayOfWeek;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $hour Hour. Defaults to 0. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $hour;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $minute Minute. Defaults to 0. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $minute;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $second Second. Defaults to 0. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $second;

    /** @var Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $millisecond Millisecond. Defaults to 0. */
    public readonly Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $millisecond;

    /** @var Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC. */
    public readonly Optional|ResolvesToString|string $timezone;

    /**
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $year Calendar year. Can be any expression that evaluates to a number.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $isoWeekYear ISO Week Date Year. Can be any expression that evaluates to a number.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $month Month. Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $isoWeek Week of year. Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $day Day of month. Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $isoDayOfWeek Day of week (Monday 1 - Sunday 7). Defaults to 1.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $hour Hour. Defaults to 0.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $minute Minute. Defaults to 0.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $second Second. Defaults to 0.
     * @param Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $millisecond Millisecond. Defaults to 0.
     * @param Optional|ResolvesToString|string $timezone The timezone to carry out the operation. $timezone must be a valid expression that resolves to a string formatted as either an Olson Timezone Identifier or a UTC Offset. If no timezone is provided, the result is displayed in UTC.
     */
    public function __construct(
        Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $year = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $isoWeekYear = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $month = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $isoWeek = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $day = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $isoDayOfWeek = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $hour = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $minute = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $second = Optional::Undefined,
        Optional|Decimal128|Int64|ResolvesToNumber|float|int|string $millisecond = Optional::Undefined,
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
}
