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
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

/**
 * Truncates a number to a whole integer or to a specified decimal place.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/trunc/
 * @internal
 */
final class TruncOperator implements ResolvesToString, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$trunc';
    public const PROPERTIES = ['number' => 'number', 'place' => 'place'];

    /**
     * @var Decimal128|Int64|ResolvesToNumber|float|int|string $number Can be any valid expression that resolves to a number. Specifically, the expression must resolve to an integer, double, decimal, or long.
     * $trunc returns an error if the expression resolves to a non-numeric data type.
     */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $number;

    /** @var Optional|ResolvesToInt|int|string $place Can be any valid expression that resolves to an integer between -20 and 100, exclusive. e.g. -20 < place < 100. Defaults to 0. */
    public readonly Optional|ResolvesToInt|int|string $place;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $number Can be any valid expression that resolves to a number. Specifically, the expression must resolve to an integer, double, decimal, or long.
     * $trunc returns an error if the expression resolves to a non-numeric data type.
     * @param Optional|ResolvesToInt|int|string $place Can be any valid expression that resolves to an integer between -20 and 100, exclusive. e.g. -20 < place < 100. Defaults to 0.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToNumber|float|int|string $number,
        Optional|ResolvesToInt|int|string $place = Optional::Undefined,
    ) {
        if (is_string($number) && ! str_starts_with($number, '$')) {
            throw new InvalidArgumentException('Argument $number can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->number = $number;
        if (is_string($place) && ! str_starts_with($place, '$')) {
            throw new InvalidArgumentException('Argument $place can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->place = $place;
    }
}
