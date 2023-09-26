<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Expression\ResolvesToBoolExpression;
use MongoDB\Builder\Expression\ResolvesToExpression;

class AndAggregation implements ResolvesToBoolExpression
{
    /** @param list<ResolvesToExpression|array|bool|float|int|object|string|null> $expressions */
    public array $expressions;

    /** @param ResolvesToExpression|array|bool|float|int|object|string|null $expressions */
    public function __construct(array|bool|float|int|null|object|string ...$expressions)
    {
        $this->expressions = $expressions;
    }
}
