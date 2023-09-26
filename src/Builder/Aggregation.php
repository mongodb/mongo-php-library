<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder;

use MongoDB\Builder\Aggregation\AndAggregation;
use MongoDB\Builder\Aggregation\EqAggregation;
use MongoDB\Builder\Aggregation\FilterAggregation;
use MongoDB\Builder\Aggregation\GtAggregation;
use MongoDB\Builder\Aggregation\LtAggregation;
use MongoDB\Builder\Aggregation\NeAggregation;
use MongoDB\Builder\Expression\ResolvesToArrayExpression;
use MongoDB\Builder\Expression\ResolvesToBoolExpression;
use MongoDB\Builder\Expression\ResolvesToExpression;

final class Aggregation
{
    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }

    /** @param ResolvesToExpression|array|bool|float|int|object|string|null $expressions */
    public static function and(array|bool|float|int|null|object|string ...$expressions): Aggregation\AndAggregation
    {
        return new AndAggregation(
            $expressions,
        );
    }

    /**
     * @param ResolvesToExpression|array|bool|float|int|object|string|null $expression1
     * @param ResolvesToExpression|array|bool|float|int|object|string|null $expression2
     */
    public static function eq(
        array|bool|float|int|null|object|string $expression1,
        array|bool|float|int|null|object|string $expression2,
    ): Aggregation\EqAggregation {
        return new EqAggregation(
            $expression1,
            $expression2,
        );
    }

    /**
     * @param ResolvesToExpression|array|bool|float|int|object|string|null $expression1
     * @param ResolvesToExpression|array|bool|float|int|object|string|null $expression2
     */
    public static function gt(
        array|bool|float|int|null|object|string $expression1,
        array|bool|float|int|null|object|string $expression2,
    ): Aggregation\GtAggregation {
        return new GtAggregation(
            $expression1,
            $expression2,
        );
    }

    /**
     * @param ResolvesToExpression|array|bool|float|int|object|string|null $expression1
     * @param ResolvesToExpression|array|bool|float|int|object|string|null $expression2
     */
    public static function lt(
        array|bool|float|int|null|object|string $expression1,
        array|bool|float|int|null|object|string $expression2,
    ): Aggregation\LtAggregation {
        return new LtAggregation(
            $expression1,
            $expression2,
        );
    }

    /**
     * @param ResolvesToExpression|array|bool|float|int|object|string|null $expression1
     * @param ResolvesToExpression|array|bool|float|int|object|string|null $expression2
     */
    public static function ne(
        array|bool|float|int|null|object|string $expression1,
        array|bool|float|int|null|object|string $expression2,
    ): Aggregation\NeAggregation {
        return new NeAggregation(
            $expression1,
            $expression2,
        );
    }

    /**
     * @param ResolvesToArrayExpression|array|object|string               $input
     * @param ResolvesToBoolExpression|array|bool|object|string           $cond
     * @param ResolvesToBoolExpression|array|float|int|object|string|null $limit
     */
    public static function filter(
        array|object|string $input,
        array|bool|object|string $cond,
        string|null $as,
        array|float|int|object|string|null $limit,
    ): Aggregation\FilterAggregation {
        return new FilterAggregation(
            $input,
            $cond,
            $as,
            $limit,
        );
    }
}
