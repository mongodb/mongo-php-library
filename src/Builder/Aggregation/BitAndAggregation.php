<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToLong;

class BitAndAggregation implements ResolvesToInt, ResolvesToLong
{
    public const NAME = '$bitAnd';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param list<Int64|ResolvesToInt|ResolvesToLong|int> ...$expression */
    public array $expression;

    /**
     * @param Int64|ResolvesToInt|ResolvesToLong|int $expression
     */
    public function __construct(Int64|ResolvesToInt|ResolvesToLong|int ...$expression)
    {
        if (! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression arguments to be a list of Int64|ResolvesToInt|ResolvesToLong|int, named arguments are not supported');
        }
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        $this->expression = $expression;
    }
}
