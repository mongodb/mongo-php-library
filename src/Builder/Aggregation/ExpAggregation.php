<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDouble;
use MongoDB\Builder\Expression\ResolvesToNumber;

/**
 * Raises e to the specified exponent.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/exp/
 */
class ExpAggregation implements ResolvesToDouble
{
    public const NAME = '$exp';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

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
