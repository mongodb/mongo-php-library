<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

/**
 * Returns the result of subtracting the second value from the first. If the two values are numbers, return the difference. If the two values are dates, return the difference in milliseconds. If the two values are a date and a number in milliseconds, return the resulting date. Accepts two argument expressions. If the two values are a date and a number, specify the date argument first as it is not meaningful to subtract a date from a number.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/subtract/
 * @internal
 */
final class SubtractOperator implements ResolvesToInt, ResolvesToLong, ResolvesToDouble, ResolvesToDecimal, ResolvesToDate, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$subtract';
    public const PROPERTIES = ['expression1' => 'expression1', 'expression2' => 'expression2'];

    /** @var DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int|string $expression1 */
    public readonly DateTimeInterface|Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int|string $expression1;

    /** @var DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int|string $expression2 */
    public readonly DateTimeInterface|Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int|string $expression2;

    /**
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int|string $expression1
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int|string $expression2
     */
    public function __construct(
        DateTimeInterface|Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int|string $expression1,
        DateTimeInterface|Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int|string $expression2,
    ) {
        if (is_string($expression1) && ! str_starts_with($expression1, '$')) {
            throw new InvalidArgumentException('Argument $expression1 can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->expression1 = $expression1;
        if (is_string($expression2) && ! str_starts_with($expression2, '$')) {
            throw new InvalidArgumentException('Argument $expression2 can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->expression2 = $expression2;
    }
}
