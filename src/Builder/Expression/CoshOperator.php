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
 * Returns the hyperbolic cosine of a value that is measured in radians.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cosh/
 */
class CoshOperator implements ResolvesToDouble, ResolvesToDecimal, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /**
     * @var Decimal128|Int64|ResolvesToNumber|float|int $expression $cosh takes any valid expression that resolves to a number, measured in radians. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the value to radians.
     * By default $cosh returns values as a double. $cosh can also return values as a 128-bit decimal if the <expression> resolves to a 128-bit decimal value.
     */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int $expression;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $cosh takes any valid expression that resolves to a number, measured in radians. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the value to radians.
     * By default $cosh returns values as a double. $cosh can also return values as a 128-bit decimal if the <expression> resolves to a 128-bit decimal value.
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int $expression)
    {
        $this->expression = $expression;
    }

    public function getOperator(): string
    {
        return '$cosh';
    }
}
