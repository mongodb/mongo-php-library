<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\Optional;

/**
 * Rounds a number to to a whole integer or to a specified decimal place.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/round/
 */
readonly class RoundOperator implements ResolvesToInt, ResolvesToDouble, ResolvesToDecimal, ResolvesToLong
{
    public const NAME = '$round';
    public const ENCODE = Encode::Array;

    /**
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $number Can be any valid expression that resolves to a number. Specifically, the expression must resolve to an integer, double, decimal, or long.
     * $round returns an error if the expression resolves to a non-numeric data type.
     */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $number;

    /** @param Optional|ResolvesToInt|int $place Can be any valid expression that resolves to an integer between -20 and 100, exclusive. */
    public Optional|ResolvesToInt|int $place;

    /**
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $number Can be any valid expression that resolves to a number. Specifically, the expression must resolve to an integer, double, decimal, or long.
     * $round returns an error if the expression resolves to a non-numeric data type.
     * @param Optional|ResolvesToInt|int $place Can be any valid expression that resolves to an integer between -20 and 100, exclusive.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|float|int $number,
        Optional|ResolvesToInt|int $place = Optional::Undefined,
    ) {
        $this->number = $number;
        $this->place = $place;
    }
}
