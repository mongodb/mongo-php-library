<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\StageInterface;

/**
 * Returns plan cache information for a collection.
 *
 * New in MongoDB 4.4
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/planCacheStats/
 * @internal
 */
final class PlanCacheStatsStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$planCacheStats';

    public function __construct()
    {
    }
}
