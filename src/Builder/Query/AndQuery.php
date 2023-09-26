<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Expression\ResolvesToBoolExpression;
use MongoDB\Builder\Expression\ResolvesToQueryOperator;

class AndQuery implements ResolvesToBoolExpression
{
    /** @param list<ResolvesToQueryOperator|array|object> $query */
    public array $query;

    /** @param ResolvesToQueryOperator|array|object $query */
    public function __construct(array|object ...$query)
    {
        $this->query = $query;
    }
}
