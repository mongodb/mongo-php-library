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
 * Returns an approximation of the median, the 50th percentile, as a scalar value.
 * New in version 7.0.
 * This operator is available as an accumulator in these stages:
 * $group
 * $setWindowFields
 * It is also available as an aggregation expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/median/
 */
class MedianAggregation implements ResolvesToDouble, AccumulatorInterface
{
    public const NAME = '$median';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Decimal128|Int64|ResolvesToNumber|float|int $input $median calculates the 50th percentile value of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $median calculation ignores it. */
    public Decimal128|Int64|ResolvesToNumber|float|int $input;

    /** @param non-empty-string $method The method that mongod uses to calculate the 50th percentile value. The method must be 'approximate'. */
    public string $method;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $input $median calculates the 50th percentile value of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $median calculation ignores it.
     * @param non-empty-string $method The method that mongod uses to calculate the 50th percentile value. The method must be 'approximate'.
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int $input, string $method)
    {
        $this->input = $input;
        $this->method = $method;
    }
}
