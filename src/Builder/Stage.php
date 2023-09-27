<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder;

use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Expression\Expression;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToObject;
use MongoDB\Builder\Stage\GroupStage;
use MongoDB\Builder\Stage\LimitStage;
use MongoDB\Builder\Stage\MatchStage;
use MongoDB\Builder\Stage\ProjectStage;
use MongoDB\Builder\Stage\SortStage;

final class Stage
{
    /**
     * @param Expression|mixed|null                                    $_id
     * @param Document|ResolvesToObject|Serializable|array|object|null $fields
     */
    public static function group(mixed $_id = null, array|null|object $fields = null): GroupStage
    {
        return new GroupStage($_id, $fields);
    }

    /** @param Int64|ResolvesToInt|int $limit */
    public static function limit(Int64|ResolvesToInt|int $limit): LimitStage
    {
        return new LimitStage($limit);
    }

    public static function match(mixed $query): MatchStage
    {
        return new MatchStage($query);
    }

    /** @param Document|ResolvesToObject|Serializable|array|object $specifications */
    public static function project(array|object $specifications): ProjectStage
    {
        return new ProjectStage($specifications);
    }

    /** @param Document|ResolvesToObject|Serializable|array|object $sortSpecification */
    public static function sort(array|object $sortSpecification): SortStage
    {
        return new SortStage($sortSpecification);
    }

    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }
}
