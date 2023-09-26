<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder;

use MongoDB\Builder\Expression\ResolvesToMatchExpression;
use MongoDB\Builder\Expression\ResolvesToSortSpecification;
use MongoDB\Builder\Stage\LimitStage;
use MongoDB\Builder\Stage\MatchStage;
use MongoDB\Builder\Stage\SortStage;

final class Stage
{
    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }

    /** @param ResolvesToMatchExpression|array|object $matchExpr */
    public static function match(array|object ...$matchExpr): Stage\MatchStage
    {
        return new MatchStage(
            $matchExpr,
        );
    }

    /** @param ResolvesToSortSpecification|array|object $sortSpecification */
    public static function sort(array|object $sortSpecification): Stage\SortStage
    {
        return new SortStage(
            $sortSpecification,
        );
    }

    public static function limit(int $limit): Stage\LimitStage
    {
        return new LimitStage(
            $limit,
        );
    }
}
