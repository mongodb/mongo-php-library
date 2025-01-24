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
 * Calculates the square root.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sqrt/
 * @internal
 */
final class SqrtOperator implements ResolvesToDouble, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$sqrt';
    public const PROPERTIES = ['number' => 'number'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $number The argument can be any valid expression as long as it resolves to a non-negative number. */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $number;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $number The argument can be any valid expression as long as it resolves to a non-negative number.
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int|string $number)
    {
        if (is_string($number) && ! str_starts_with($number, '$')) {
            throw new InvalidArgumentException('Argument $number can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->number = $number;
    }
}
