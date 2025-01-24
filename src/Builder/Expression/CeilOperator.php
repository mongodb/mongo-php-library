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
 * Returns the smallest integer greater than or equal to the specified number.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ceil/
 * @internal
 */
final class CeilOperator implements ResolvesToInt, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$ceil';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $expression If the argument resolves to a value of null or refers to a field that is missing, $ceil returns null. If the argument resolves to NaN, $ceil returns NaN. */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $expression;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $expression If the argument resolves to a value of null or refers to a field that is missing, $ceil returns null. If the argument resolves to NaN, $ceil returns NaN.
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int|string $expression)
    {
        if (is_string($expression) && ! str_starts_with($expression, '$')) {
            throw new InvalidArgumentException('Argument $expression can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->expression = $expression;
    }
}
