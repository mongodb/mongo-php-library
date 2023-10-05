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
 * Calculates the natural log of a number.
 * $ln is equivalent to $log: [ <number>, Math.E ] expression, where Math.E is a JavaScript representation for Euler's number e.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ln/
 */
class LnAggregation implements ResolvesToDouble
{
    public const NAME = '$ln';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Decimal128|Int64|ResolvesToNumber|float|int $number Any valid expression as long as it resolves to a non-negative number. For more information on expressions, see Expressions. */
    public Decimal128|Int64|ResolvesToNumber|float|int $number;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number Any valid expression as long as it resolves to a non-negative number. For more information on expressions, see Expressions.
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int $number)
    {
        $this->number = $number;
    }
}
