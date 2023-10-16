<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;

/**
 * Raises e to the specified exponent.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/exp/
 */
readonly class ExpOperator implements ResolvesToDouble
{
    public const NAME = '$exp';
    public const ENCODE = Encode::Single;

    /** @param Decimal128|Int64|ResolvesToNumber|float|int $exponent */
    public Decimal128|Int64|ResolvesToNumber|float|int $exponent;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $exponent
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int $exponent)
    {
        $this->exponent = $exponent;
    }
}
