<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\WindowInterface;

/**
 * Returns the population covariance of two numeric expressions.
 * New in MongoDB 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/covariancePop/
 * @internal
 */
final class CovariancePopAccumulator implements WindowInterface, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$covariancePop';
    public const PROPERTIES = ['expression1' => 'expression1', 'expression2' => 'expression2'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $expression1 */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $expression1;

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $expression2 */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $expression2;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $expression1
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $expression2
     */
    public function __construct(
        Decimal128|Int64|ResolvesToNumber|float|int|string $expression1,
        Decimal128|Int64|ResolvesToNumber|float|int|string $expression2,
    ) {
        $this->expression1 = $expression1;
        $this->expression2 = $expression2;
    }
}
