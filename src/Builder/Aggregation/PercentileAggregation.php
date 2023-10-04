<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Model\BSONArray;

class PercentileAggregation implements ResolvesToArray, AccumulatorInterface
{
    public const NAME = '$percentile';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Decimal128|Int64|ResolvesToNumber|float|int $input $percentile calculates the percentile values of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $percentile calculation ignores it. */
    public Decimal128|Int64|ResolvesToNumber|float|int $input;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $p $percentile calculates a percentile value for each element in p. The elements represent percentages and must evaluate to numeric values in the range 0.0 to 1.0, inclusive.
     * $percentile returns results in the same order as the elements in p.
     */
    public PackedArray|ResolvesToArray|BSONArray|array $p;

    /** @param non-empty-string $method The method that mongod uses to calculate the percentile value. The method must be 'approximate'. */
    public string $method;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $input $percentile calculates the percentile values of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $percentile calculation ignores it.
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $p $percentile calculates a percentile value for each element in p. The elements represent percentages and must evaluate to numeric values in the range 0.0 to 1.0, inclusive.
     * $percentile returns results in the same order as the elements in p.
     * @param non-empty-string $method The method that mongod uses to calculate the percentile value. The method must be 'approximate'.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToNumber|float|int $input,
        PackedArray|ResolvesToArray|BSONArray|array $p,
        string $method,
    ) {
        $this->input = $input;
        if (\is_array($p) && ! \array_is_list($p)) {
            throw new \InvalidArgumentException('Expected $p argument to be a list, got an associative array.');
        }
        $this->p = $p;
        $this->method = $method;
    }
}
