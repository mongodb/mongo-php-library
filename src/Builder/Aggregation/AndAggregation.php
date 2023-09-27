<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use InvalidArgumentException;
use MongoDB\Builder\Expression\Expression;
use MongoDB\Builder\Expression\ResolvesToBool;

use function count;
use function sprintf;

class AndAggregation implements ResolvesToBool
{
    /** @param list<Expression|mixed> ...$expressions */
    public array $expressions;

    public function __construct(mixed ...$expressions)
    {
        if (count($expressions) < 1) {
            throw new InvalidArgumentException(sprintf('Expected at least %d values, got %d.', 1, count($expressions)));
        }

        $this->expressions = $expressions;
    }
}
