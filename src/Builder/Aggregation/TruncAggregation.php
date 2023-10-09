<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDecimal;
use MongoDB\Builder\Expression\ResolvesToDouble;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToLong;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Optional;

/**
 * Truncates a number to a whole integer or to a specified decimal place.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/trunc/
 */
class TruncAggregation implements ResolvesToString
{
    public const NAME = '$trunc';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /**
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $number Can be any valid expression that resolves to a number. Specifically, the expression must resolve to an integer, double, decimal, or long.
     * $trunc returns an error if the expression resolves to a non-numeric data type.
     */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $number;

    /** @param Optional|ResolvesToInt|int $place Can be any valid expression that resolves to an integer between -20 and 100, exclusive. e.g. -20 < place < 100. Defaults to 0. */
    public ResolvesToInt|Optional|int $place;

    /**
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $number Can be any valid expression that resolves to a number. Specifically, the expression must resolve to an integer, double, decimal, or long.
     * $trunc returns an error if the expression resolves to a non-numeric data type.
     * @param Optional|ResolvesToInt|int $place Can be any valid expression that resolves to an integer between -20 and 100, exclusive. e.g. -20 < place < 100. Defaults to 0.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $number,
        ResolvesToInt|Optional|int $place = Optional::Undefined,
    ) {
        $this->number = $number;
        $this->place = $place;
    }
}
