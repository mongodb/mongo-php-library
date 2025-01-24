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
 * Returns the remainder of the first number divided by the second. Accepts two argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mod/
 * @internal
 */
final class ModOperator implements ResolvesToInt, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$mod';
    public const PROPERTIES = ['dividend' => 'dividend', 'divisor' => 'divisor'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $dividend The first argument is the dividend, and the second argument is the divisor; i.e. first argument is divided by the second argument. */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $dividend;

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $divisor */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $divisor;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $dividend The first argument is the dividend, and the second argument is the divisor; i.e. first argument is divided by the second argument.
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $divisor
     */
    public function __construct(
        Decimal128|Int64|ResolvesToNumber|float|int|string $dividend,
        Decimal128|Int64|ResolvesToNumber|float|int|string $divisor,
    ) {
        if (is_string($dividend) && ! str_starts_with($dividend, '$')) {
            throw new InvalidArgumentException('Argument $dividend can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->dividend = $dividend;
        if (is_string($divisor) && ! str_starts_with($divisor, '$')) {
            throw new InvalidArgumentException('Argument $divisor can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->divisor = $divisor;
    }
}
