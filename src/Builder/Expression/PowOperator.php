<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Raises a number to the specified exponent.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/pow/
 */
class PowOperator implements ResolvesToNumber, OperatorInterface
{
    public const ENCODE = Encode::Array;

    /** @var Decimal128|Int64|ResolvesToNumber|float|int $number */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int $number;

    /** @var Decimal128|Int64|ResolvesToNumber|float|int $exponent */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int $exponent;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $number
     * @param Decimal128|Int64|ResolvesToNumber|float|int $exponent
     */
    public function __construct(
        Decimal128|Int64|ResolvesToNumber|float|int $number,
        Decimal128|Int64|ResolvesToNumber|float|int $exponent,
    ) {
        $this->number = $number;
        $this->exponent = $exponent;
    }

    public function getOperator(): string
    {
        return '$pow';
    }
}
