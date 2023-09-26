<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder;

use MongoDB\Builder\Expression\ResolvesToExpression;
use MongoDB\Builder\Expression\ResolvesToQueryOperator;
use MongoDB\Builder\Query\AndQuery;
use MongoDB\Builder\Query\ExprQuery;

final class Query
{
    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }

    /** @param ResolvesToQueryOperator|array|object $query */
    public static function and(array|object ...$query): Query\AndQuery
    {
        return new AndQuery(
            $query,
        );
    }

    /** @param ResolvesToExpression|array|bool|float|int|object|string|null $expression */
    public static function expr(array|bool|float|int|null|object|string $expression): Query\ExprQuery
    {
        return new ExprQuery(
            $expression,
        );
    }
}
