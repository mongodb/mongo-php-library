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
 * Returns statistics regarding the use of each index for the collection.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexStats/
 */
class IndexStatsStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    public function __construct()
    {
    }

    public function getOperator(): string
    {
        return '$indexStats';
    }
}
