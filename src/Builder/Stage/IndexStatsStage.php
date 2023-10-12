<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\StageInterface;

/**
 * Returns statistics regarding the use of each index for the collection.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexStats/
 */
readonly class IndexStatsStage implements StageInterface
{
    public const NAME = '$indexStats';
    public const ENCODE = Encode::Object;

    public function __construct()
    {
    }
}
