<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToDate;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Optional;

/**
 * Converts a date/time string to a date object.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromString/
 */
class DateFromStringAggregation implements ResolvesToDate
{
    public const NAME = '$dateFromString';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ResolvesToString|non-empty-string $dateString The date/time string to convert to a date object. */
    public ResolvesToString|string $dateString;

    /**
     * @param Optional|ResolvesToString|non-empty-string $format The date format specification of the dateString. The format can be any expression that evaluates to a string literal, containing 0 or more format specifiers.
     * If unspecified, $dateFromString uses "%Y-%m-%dT%H:%M:%S.%LZ" as the default format but accepts a variety of formats and attempts to parse the dateString if possible.
     */
    public ResolvesToString|Optional|string $format;

    /** @param Optional|ResolvesToString|non-empty-string $timezone The time zone to use to format the date. */
    public ResolvesToString|Optional|string $timezone;

    /**
     * @param ExpressionInterface|Optional|mixed $onError If $dateFromString encounters an error while parsing the given dateString, it outputs the result value of the provided onError expression. This result value can be of any type.
     * If you do not specify onError, $dateFromString throws an error if it cannot parse dateString.
     */
    public mixed $onError;

    /**
     * @param ExpressionInterface|Optional|mixed $onNull If the dateString provided to $dateFromString is null or missing, it outputs the result value of the provided onNull expression. This result value can be of any type.
     * If you do not specify onNull and dateString is null or missing, then $dateFromString outputs null.
     */
    public mixed $onNull;

    /**
     * @param ResolvesToString|non-empty-string $dateString The date/time string to convert to a date object.
     * @param Optional|ResolvesToString|non-empty-string $format The date format specification of the dateString. The format can be any expression that evaluates to a string literal, containing 0 or more format specifiers.
     * If unspecified, $dateFromString uses "%Y-%m-%dT%H:%M:%S.%LZ" as the default format but accepts a variety of formats and attempts to parse the dateString if possible.
     * @param Optional|ResolvesToString|non-empty-string $timezone The time zone to use to format the date.
     * @param ExpressionInterface|Optional|mixed $onError If $dateFromString encounters an error while parsing the given dateString, it outputs the result value of the provided onError expression. This result value can be of any type.
     * If you do not specify onError, $dateFromString throws an error if it cannot parse dateString.
     * @param ExpressionInterface|Optional|mixed $onNull If the dateString provided to $dateFromString is null or missing, it outputs the result value of the provided onNull expression. This result value can be of any type.
     * If you do not specify onNull and dateString is null or missing, then $dateFromString outputs null.
     */
    public function __construct(
        ResolvesToString|string $dateString,
        ResolvesToString|Optional|string $format = Optional::Undefined,
        ResolvesToString|Optional|string $timezone = Optional::Undefined,
        mixed $onError = Optional::Undefined,
        mixed $onNull = Optional::Undefined,
    ) {
        $this->dateString = $dateString;
        $this->format = $format;
        $this->timezone = $timezone;
        $this->onError = $onError;
        $this->onNull = $onNull;
    }
}
