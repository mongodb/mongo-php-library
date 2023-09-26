<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Expression\ResolvesToExpression;

class ExprQuery implements ResolvesToExpression
{
    public array|bool|float|int|null|object|string $expression;

    /** @param ResolvesToExpression|array|bool|float|int|object|string|null $expression */
    public function __construct(array|bool|float|int|null|object|string $expression)
    {
        $this->expression = $expression;
    }
}
