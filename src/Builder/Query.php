<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder;

use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToBool;
use MongoDB\Builder\Query\AndQuery;
use MongoDB\Builder\Query\ExprQuery;
use MongoDB\Builder\Query\GtQuery;
use MongoDB\Builder\Query\GteQuery;
use MongoDB\Builder\Query\LtQuery;
use MongoDB\Builder\Query\OrQuery;

final class Query
{
    /**
     * @param ResolvesToBool|bool ...$query
     */
    public static function and(ResolvesToBool|bool ...$query): AndQuery
    {
        return new AndQuery(...$query);
    }

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public static function expr(mixed $expression): ExprQuery
    {
        return new ExprQuery($expression);
    }

    /**
     * @param ExpressionInterface|mixed $value
     */
    public static function gt(mixed $value): GtQuery
    {
        return new GtQuery($value);
    }

    /**
     * @param ExpressionInterface|mixed $value
     */
    public static function gte(mixed $value): GteQuery
    {
        return new GteQuery($value);
    }

    /**
     * @param ExpressionInterface|mixed $value
     */
    public static function lt(mixed $value): LtQuery
    {
        return new LtQuery($value);
    }

    /**
     * @param ExpressionInterface|mixed ...$query
     */
    public static function or(mixed ...$query): OrQuery
    {
        return new OrQuery(...$query);
    }

    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }
}
