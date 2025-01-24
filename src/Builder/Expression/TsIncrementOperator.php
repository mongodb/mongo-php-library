<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Timestamp;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

/**
 * Returns the incrementing ordinal from a timestamp as a long.
 * New in MongoDB 5.1.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsIncrement/
 * @internal
 */
final class TsIncrementOperator implements ResolvesToLong, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$tsIncrement';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var ResolvesToTimestamp|Timestamp|int|string $expression */
    public readonly Timestamp|ResolvesToTimestamp|int|string $expression;

    /**
     * @param ResolvesToTimestamp|Timestamp|int|string $expression
     */
    public function __construct(Timestamp|ResolvesToTimestamp|int|string $expression)
    {
        if (is_string($expression) && ! str_starts_with($expression, '$')) {
            throw new InvalidArgumentException('Argument $expression can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->expression = $expression;
    }
}
