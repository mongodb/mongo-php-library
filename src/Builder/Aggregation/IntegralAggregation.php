<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\UTCDateTime;
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
 * Returns the approximation of the area under a curve.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/integral/
 */
class IntegralAggregation implements ResolvesToDouble, ResolvesToDecimal
{
    public const NAME = '$integral';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Decimal128|Int64|ResolvesToDate|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|UTCDateTime|float|int $input */
    public Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $input;

    /**
     * @param Optional|ResolvesToString|non-empty-string $unit A string that specifies the time unit. Use one of these strings: "week", "day","hour", "minute", "second", "millisecond".
     * If the sortBy field is not a date, you must omit a unit. If you specify a unit, you must specify a date in the sortBy field.
     */
    public ResolvesToString|Optional|string $unit;

    /**
     * @param Decimal128|Int64|ResolvesToDate|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|UTCDateTime|float|int $input
     * @param Optional|ResolvesToString|non-empty-string $unit A string that specifies the time unit. Use one of these strings: "week", "day","hour", "minute", "second", "millisecond".
     * If the sortBy field is not a date, you must omit a unit. If you specify a unit, you must specify a date in the sortBy field.
     */
    public function __construct(
        Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $input,
        ResolvesToString|Optional|string $unit = Optional::Undefined,
    ) {
        $this->input = $input;
        $this->unit = $unit;
    }
}
