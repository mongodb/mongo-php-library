<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToLong;

/**
 * Returns the result of a bitwise xor (exclusive or) operation on an array of int and long values.
 * New in version 6.3.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitXor/
 */
class BitXorAggregation implements ResolvesToInt, ResolvesToLong
{
    public const NAME = '$bitXor';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /**
     * @no-named-arguments
     * @param list<Int64|ResolvesToInt|ResolvesToLong|int> ...$expression
     */
    public array $expression;

    /**
     * @param Int64|ResolvesToInt|ResolvesToLong|int ...$expression
     */
    public function __construct(Int64|ResolvesToInt|ResolvesToLong|int ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression arguments to be a list of Int64|ResolvesToInt|ResolvesToLong|int, named arguments are not supported');
        }
        $this->expression = $expression;
    }
}
