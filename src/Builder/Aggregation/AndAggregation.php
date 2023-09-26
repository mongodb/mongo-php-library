<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use InvalidArgumentException;
use MongoDB\Builder\Expression\ResolvesToBoolExpression;
use MongoDB\Builder\Expression\ResolvesToExpression;

use function count;
use function sprintf;

class AndAggregation implements ResolvesToBoolExpression
{
    /** @param list<ResolvesToExpression|array|bool|float|int|object|string|null> $expressions */
    public array $expressions;

    /** @param ResolvesToExpression|array|bool|float|int|object|string|null $expressions */
    public function __construct(array|bool|float|int|null|object|string ...$expressions)
    {
        if (count($expressions) < 1) {
            throw new InvalidArgumentException(sprintf('Expected at least %d values, got %d.', 1, count($expressions)));
        }

        $this->expressions = $expressions;
    }
}
