<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Exception\InvalidArgumentException;

use function array_is_list;

/**
 * Returns the result of a bitwise xor (exclusive or) operation on an array of int and long values.
 * New in version 6.3.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitXor/
 */
class BitXorOperator implements ResolvesToInt, ResolvesToLong
{
    public const NAME = '$bitXor';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param list<Int64|ResolvesToInt|ResolvesToLong|int> ...$expression */
    public array $expression;

    /**
     * @param Int64|ResolvesToInt|ResolvesToLong|int ...$expression
     * @no-named-arguments
     */
    public function __construct(Int64|ResolvesToInt|ResolvesToLong|int ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }
        $this->expression = $expression;
    }
}