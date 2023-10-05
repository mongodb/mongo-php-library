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
use MongoDB\Builder\Optional;

/**
 * Returns the exponential moving average for the numeric expression.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/expMovingAvg/
 */
class ExpMovingAvgAggregation implements ResolvesToDouble
{
    public const NAME = '$expMovingAvg';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Decimal128|Int64|ResolvesToNumber|float|int $input */
    public Decimal128|Int64|ResolvesToNumber|float|int $input;

    /**
     * @param Int64|Optional|int $N An integer that specifies the number of historical documents that have a significant mathematical weight in the exponential moving average calculation, with the most recent documents contributing the most weight.
     * You must specify either N or alpha. You cannot specify both.
     * The N value is used in this formula to calculate the current result based on the expression value from the current document being read and the previous result of the calculation:
     */
    public Int64|Optional|int $N;

    /**
     * @param Int64|Optional|float|int $alpha A double that specifies the exponential decay value to use in the exponential moving average calculation. A higher alpha value assigns a lower mathematical significance to previous results from the calculation.
     * You must specify either N or alpha. You cannot specify both.
     */
    public Int64|Optional|float|int $alpha;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $input
     * @param Int64|Optional|int $N An integer that specifies the number of historical documents that have a significant mathematical weight in the exponential moving average calculation, with the most recent documents contributing the most weight.
     * You must specify either N or alpha. You cannot specify both.
     * The N value is used in this formula to calculate the current result based on the expression value from the current document being read and the previous result of the calculation:
     * @param Int64|Optional|float|int $alpha A double that specifies the exponential decay value to use in the exponential moving average calculation. A higher alpha value assigns a lower mathematical significance to previous results from the calculation.
     * You must specify either N or alpha. You cannot specify both.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToNumber|float|int $input,
        Int64|Optional|int $N = Optional::Undefined,
        Int64|Optional|float|int $alpha = Optional::Undefined,
    ) {
        $this->input = $input;
        $this->N = $N;
        $this->alpha = $alpha;
    }
}
