<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use DateTimeInterface;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDate;
use MongoDB\Builder\Expression\ResolvesToNumber;

/**
 * Returns the result of subtracting the second value from the first. If the two values are numbers, return the difference. If the two values are dates, return the difference in milliseconds. If the two values are a date and a number in milliseconds, return the resulting date. Accepts two argument expressions. If the two values are a date and a number, specify the date argument first as it is not meaningful to subtract a date from a number.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/subtract/
 */
class SubtractAggregation implements ResolvesToNumber, ResolvesToDate
{
    public const NAME = '$subtract';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression1 */
    public DateTimeInterface|Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int $expression1;

    /** @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression2 */
    public DateTimeInterface|Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int $expression2;

    /**
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression1
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression2
     */
    public function __construct(
        DateTimeInterface|Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int $expression1,
        DateTimeInterface|Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int $expression2,
    ) {
        $this->expression1 = $expression1;
        $this->expression2 = $expression2;
    }
}