<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;

/**
 * Calculates the log base 10 of a number.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/log10/
 */
class Log10Operator implements ResolvesToDouble
{
    public const NAME = '$log10';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $number Any valid expression as long as it resolves to a non-negative number. */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $number;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $number Any valid expression as long as it resolves to a non-negative number.
     */
    public function __construct(Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $number)
    {
        $this->number = $number;
    }
}
