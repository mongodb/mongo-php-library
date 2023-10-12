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
 * Truncates a number to a whole integer or to a specified decimal place.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/trunc/
 */
readonly class TruncOperator implements ResolvesToString
{
    public const NAME = '$trunc';
    public const ENCODE = Encode::Array;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $number Can be any valid expression that resolves to a number. Specifically, the expression must resolve to an integer, double, decimal, or long.
     * $trunc returns an error if the expression resolves to a non-numeric data type.
     */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $number;

    /** @param Optional|ResolvesToInt|int $place Can be any valid expression that resolves to an integer between -20 and 100, exclusive. e.g. -20 < place < 100. Defaults to 0. */
    public Optional|ResolvesToInt|int $place;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $number Can be any valid expression that resolves to a number. Specifically, the expression must resolve to an integer, double, decimal, or long.
     * $trunc returns an error if the expression resolves to a non-numeric data type.
     * @param Optional|ResolvesToInt|int $place Can be any valid expression that resolves to an integer between -20 and 100, exclusive. e.g. -20 < place < 100. Defaults to 0.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $number,
        Optional|ResolvesToInt|int $place = Optional::Undefined,
    ) {
        $this->number = $number;
        $this->place = $place;
    }
}
