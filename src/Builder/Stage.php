<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder;

use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Stage\GroupStage;
use MongoDB\Builder\Stage\LimitStage;
use MongoDB\Builder\Stage\MatchStage;
use MongoDB\Builder\Stage\ProjectStage;
use MongoDB\Builder\Stage\SortStage;

final class Stage
{
    /**
     * @param ExpressionInterface|mixed|null $_id
     * @param ExpressionInterface|mixed ...$fields
     */
    public static function group(mixed $_id, mixed ...$fields): GroupStage
    {
        return new GroupStage($_id, ...$fields);
    }

    /**
     * @param Int64|int $limit
     */
    public static function limit(Int64|int $limit): LimitStage
    {
        return new LimitStage($limit);
    }

    /**
     * @param ExpressionInterface|mixed $query
     */
    public static function match(mixed $query): MatchStage
    {
        return new MatchStage($query);
    }

    /**
     * @param ExpressionInterface|mixed ...$specifications
     */
    public static function project(mixed ...$specifications): ProjectStage
    {
        return new ProjectStage(...$specifications);
    }

    /**
     * @param Document|Int64|Serializable|array|int|object ...$sortSpecification
     */
    public static function sort(array|int|object ...$sortSpecification): SortStage
    {
        return new SortStage(...$sortSpecification);
    }

    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }
}
