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
 * Calculates the log of a number in the specified base.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/log/
 * @internal
 */
final class LogOperator implements ResolvesToDouble, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$log';
    public const PROPERTIES = ['number' => 'number', 'base' => 'base'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $number Any valid expression as long as it resolves to a non-negative number. */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $number;

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $base Any valid expression as long as it resolves to a positive number greater than 1. */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $base;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $number Any valid expression as long as it resolves to a non-negative number.
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $base Any valid expression as long as it resolves to a positive number greater than 1.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToNumber|float|int|string $number,
        Decimal128|Int64|ResolvesToNumber|float|int|string $base,
    ) {
        if (is_string($number) && ! str_starts_with($number, '$')) {
            throw new InvalidArgumentException('Argument $number can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->number = $number;
        if (is_string($base) && ! str_starts_with($base, '$')) {
            throw new InvalidArgumentException('Argument $base can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->base = $base;
    }
}
