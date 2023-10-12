<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\StageInterface;

/**
 * Returns plan cache information for a collection.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/planCacheStats/
 */
readonly class PlanCacheStatsStage implements StageInterface
{
    public const NAME = '$planCacheStats';
    public const ENCODE = Encode::Object;

    public function __construct()
    {
    }
}
