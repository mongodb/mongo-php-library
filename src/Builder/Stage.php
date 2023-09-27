<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder;

use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToObject;
use MongoDB\Builder\Stage\LimitStage;
use MongoDB\Builder\Stage\MatchStage;
use MongoDB\Builder\Stage\SortStage;

final class Stage
{
    /** @param Int64|ResolvesToInt|int $limit */
    public static function limit(Int64|ResolvesToInt|int $limit): LimitStage
    {
        return new LimitStage($limit);
    }

    public static function match(mixed ...$matchExpr): MatchStage
    {
        return new MatchStage(...$matchExpr);
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
