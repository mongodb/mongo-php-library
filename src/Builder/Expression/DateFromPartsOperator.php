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
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

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
        if (is_string($year) && ! str_starts_with($year, '$')) {
            throw new InvalidArgumentException('Argument $year can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->year = $year;
        if (is_string($isoWeekYear) && ! str_starts_with($isoWeekYear, '$')) {
            throw new InvalidArgumentException('Argument $isoWeekYear can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->isoWeekYear = $isoWeekYear;
        if (is_string($month) && ! str_starts_with($month, '$')) {
            throw new InvalidArgumentException('Argument $month can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->month = $month;
        if (is_string($isoWeek) && ! str_starts_with($isoWeek, '$')) {
            throw new InvalidArgumentException('Argument $isoWeek can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->isoWeek = $isoWeek;
        if (is_string($day) && ! str_starts_with($day, '$')) {
            throw new InvalidArgumentException('Argument $day can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->day = $day;
        if (is_string($isoDayOfWeek) && ! str_starts_with($isoDayOfWeek, '$')) {
            throw new InvalidArgumentException('Argument $isoDayOfWeek can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->isoDayOfWeek = $isoDayOfWeek;
        if (is_string($hour) && ! str_starts_with($hour, '$')) {
            throw new InvalidArgumentException('Argument $hour can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->hour = $hour;
        if (is_string($minute) && ! str_starts_with($minute, '$')) {
            throw new InvalidArgumentException('Argument $minute can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->minute = $minute;
        if (is_string($second) && ! str_starts_with($second, '$')) {
            throw new InvalidArgumentException('Argument $second can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->second = $second;
        if (is_string($millisecond) && ! str_starts_with($millisecond, '$')) {
            throw new InvalidArgumentException('Argument $millisecond can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->millisecond = $millisecond;
        $this->timezone = $timezone;
    }
}
