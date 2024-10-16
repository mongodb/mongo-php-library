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
 * Provides data and size distribution information on sharded collections.
 * New in MongoDB 6.0.3.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shardedDataDistribution/
 */
class ShardedDataDistributionStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    public function __construct()
    {
    }

    public function getOperator(): string
    {
        return '$shardedDataDistribution';
    }
}
