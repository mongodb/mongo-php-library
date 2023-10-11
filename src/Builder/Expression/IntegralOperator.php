<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\Optional;

/**
 * Returns the approximation of the area under a curve.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/integral/
 */
class IntegralOperator implements ResolvesToDouble, ResolvesToDecimal
{
    public const NAME = '$integral';
    public const ENCODE = Encode::Object;

    /** @param Decimal128|Int64|ResolvesToDate|ResolvesToInt|ResolvesToNumber|UTCDateTime|float|int $input */
    public Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToInt|ResolvesToNumber|float|int $input;

    /**
     * @param Optional|ResolvesToString|non-empty-string $unit A string that specifies the time unit. Use one of these strings: "week", "day","hour", "minute", "second", "millisecond".
     * If the sortBy field is not a date, you must omit a unit. If you specify a unit, you must specify a date in the sortBy field.
     */
    public Optional|ResolvesToString|string $unit;

    /**
     * @param Decimal128|Int64|ResolvesToDate|ResolvesToInt|ResolvesToNumber|UTCDateTime|float|int $input
     * @param Optional|ResolvesToString|non-empty-string $unit A string that specifies the time unit. Use one of these strings: "week", "day","hour", "minute", "second", "millisecond".
     * If the sortBy field is not a date, you must omit a unit. If you specify a unit, you must specify a date in the sortBy field.
     */
    public function __construct(
        Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToInt|ResolvesToNumber|float|int $input,
        Optional|ResolvesToString|string $unit = Optional::Undefined,
    ) {
        $this->input = $input;
        $this->unit = $unit;
    }
}
