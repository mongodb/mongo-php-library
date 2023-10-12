<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\WindowInterface;

/**
 * Returns the exponential moving average for the numeric expression.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/expMovingAvg/
 */
readonly class ExpMovingAvgAccumulator implements WindowInterface
{
    public const NAME = '$expMovingAvg';
    public const ENCODE = Encode::Object;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $input */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $input;

    /**
     * @param Optional|int $N An integer that specifies the number of historical documents that have a significant mathematical weight in the exponential moving average calculation, with the most recent documents contributing the most weight.
     * You must specify either N or alpha. You cannot specify both.
     * The N value is used in this formula to calculate the current result based on the expression value from the current document being read and the previous result of the calculation:
     */
    public Optional|int $N;

    /**
     * @param Optional|Int64|float|int $alpha A double that specifies the exponential decay value to use in the exponential moving average calculation. A higher alpha value assigns a lower mathematical significance to previous results from the calculation.
     * You must specify either N or alpha. You cannot specify both.
     */
    public Optional|Int64|float|int $alpha;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $input
     * @param Optional|int $N An integer that specifies the number of historical documents that have a significant mathematical weight in the exponential moving average calculation, with the most recent documents contributing the most weight.
     * You must specify either N or alpha. You cannot specify both.
     * The N value is used in this formula to calculate the current result based on the expression value from the current document being read and the previous result of the calculation:
     * @param Optional|Int64|float|int $alpha A double that specifies the exponential decay value to use in the exponential moving average calculation. A higher alpha value assigns a lower mathematical significance to previous results from the calculation.
     * You must specify either N or alpha. You cannot specify both.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $input,
        Optional|int $N = Optional::Undefined,
        Optional|Int64|float|int $alpha = Optional::Undefined,
    ) {
        $this->input = $input;
        $this->N = $N;
        $this->alpha = $alpha;
    }
}
