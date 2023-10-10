<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;

/**
 * Raises e to the specified exponent.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/exp/
 */
class ExpOperator implements ResolvesToDouble
{
    public const NAME = '$exp';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $exponent */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $exponent;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $exponent
     */
    public function __construct(Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $exponent)
    {
        $this->exponent = $exponent;
    }
}
