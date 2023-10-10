<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\WindowInterface;

/**
 * Returns the population covariance of two numeric expressions.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/covariancePop/
 */
class CovariancePopOperator implements WindowInterface
{
    public const NAME = '$covariancePop';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression1 */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression1;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression2 */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression2;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression1
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression2
     */
    public function __construct(
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression1,
        Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression2,
    ) {
        $this->expression1 = $expression1;
        $this->expression2 = $expression2;
    }
}
