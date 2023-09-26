<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Expression\ResolvesToArrayExpression;
use MongoDB\Builder\Expression\ResolvesToBoolExpression;

class FilterAggregation implements ResolvesToArrayExpression
{
    public array|object|string $input;
    public array|bool|object|string $cond;
    public string|null $as;
    public array|float|int|object|string|null $limit;

    /**
     * @param ResolvesToArrayExpression|array|object|string               $input
     * @param ResolvesToBoolExpression|array|bool|object|string           $cond
     * @param ResolvesToBoolExpression|array|float|int|object|string|null $limit
     */
    public function __construct(
        array|object|string $input,
        array|bool|object|string $cond,
        string|null $as,
        array|float|int|object|string|null $limit,
    ) {
        $this->input = $input;
        $this->cond = $cond;
        $this->as = $as;
        $this->limit = $limit;
    }
}
