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
 * Returns the sigmoid of a value, defined as 1 / (1 + e^(-x)). The result is a value between 0 and 1.
 *
 * New in MongoDB 8.1
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sigmoid/
 * @internal
 */
final class SigmoidOperator implements ResolvesToDouble, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$sigmoid';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $expression $sigmoid takes any valid expression that resolves to a number. */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $expression;

    /** @param Decimal128|Int64|ResolvesToNumber|float|int|string $expression $sigmoid takes any valid expression that resolves to a number. */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int|string $expression)
    {
        if (is_string($expression) && ! str_starts_with($expression, '$')) {
            throw new InvalidArgumentException('Argument $expression can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->expression = $expression;
    }
}
