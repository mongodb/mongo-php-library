<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Expression\ResolvesToBoolExpression;
use MongoDB\Builder\Expression\ResolvesToExpression;

class NeAggregation implements ResolvesToBoolExpression
{
    public array|bool|float|int|null|object|string $expression1;
    public array|bool|float|int|null|object|string $expression2;

    /**
     * @param ResolvesToExpression|array|bool|float|int|object|string|null $expression1
     * @param ResolvesToExpression|array|bool|float|int|object|string|null $expression2
     */
    public function __construct(
        array|bool|float|int|null|object|string $expression1,
        array|bool|float|int|null|object|string $expression2,
    ) {
        $this->expression1 = $expression1;
        $this->expression2 = $expression2;
    }
}
