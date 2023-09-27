<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder;

use MongoDB\Builder\Expression\ResolvesToBool;
use MongoDB\Builder\Query\AndQuery;
use MongoDB\Builder\Query\ExprQuery;

final class Query
{
    public static function and(ResolvesToBool|bool ...$query): AndQuery
    {
        return new AndQuery(...$query);
    }

    public static function expr(mixed $expression): ExprQuery
    {
        return new ExprQuery($expression);
    }

    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }
}
