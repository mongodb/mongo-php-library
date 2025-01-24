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
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

/**
 * Raises a number to the specified exponent.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/pow/
 * @internal
 */
final class PowOperator implements ResolvesToNumber, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$pow';
    public const PROPERTIES = ['number' => 'number', 'exponent' => 'exponent'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $number */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $number;

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $exponent */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $exponent;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $number
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $exponent
     */
    public function __construct(
        Decimal128|Int64|ResolvesToNumber|float|int|string $number,
        Decimal128|Int64|ResolvesToNumber|float|int|string $exponent,
    ) {
        if (is_string($number) && ! str_starts_with($number, '$')) {
            throw new InvalidArgumentException('Argument $number can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->number = $number;
        if (is_string($exponent) && ! str_starts_with($exponent, '$')) {
            throw new InvalidArgumentException('Argument $exponent can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->exponent = $exponent;
    }
}
