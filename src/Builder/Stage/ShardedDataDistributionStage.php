<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\StageInterface;

/**
 * Provides data and size distribution information on sharded collections.
 * New in version 6.0.3.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shardedDataDistribution/
 */
class ShardedDataDistributionStage implements StageInterface
{
    public const NAME = '$shardedDataDistribution';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    public function __construct()
    {
    }
}
