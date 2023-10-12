<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;

/**
 * Returns the result of dividing the first number by the second. Accepts two argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/divide/
 */
readonly class DivideOperator implements ResolvesToDouble
{
    public const NAME = '$divide';
    public const ENCODE = Encode::Array;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $dividend The first argument is the dividend, and the second argument is the divisor; i.e. the first argument is divided by the second argument. */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $dividend;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $divisor */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $divisor;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $dividend The first argument is the dividend, and the second argument is the divisor; i.e. the first argument is divided by the second argument.
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $divisor
     */
    public function __construct(
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $dividend,
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $divisor,
    ) {
        $this->dividend = $dividend;
        $this->divisor = $divisor;
    }
}
