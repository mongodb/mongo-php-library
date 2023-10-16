<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;

/**
 * Raises a number to the specified exponent.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/pow/
 */
readonly class PowOperator implements ResolvesToNumber
{
    public const NAME = '$pow';
    public const ENCODE = Encode::Array;

    /** @param Decimal128|Int64|ResolvesToNumber|float|int $number */
    public Decimal128|Int64|ResolvesToNumber|float|int $number;

    /** @param Decimal128|Int64|ResolvesToNumber|float|int $exponent */
    public Decimal128|Int64|ResolvesToNumber|float|int $exponent;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number
     * @param Decimal128|Int64|ResolvesToNumber|float|int $exponent
     */
    public function __construct(
        Decimal128|Int64|ResolvesToNumber|float|int $number,
        Decimal128|Int64|ResolvesToNumber|float|int $exponent,
    ) {
        $this->number = $number;
        $this->exponent = $exponent;
    }
}
