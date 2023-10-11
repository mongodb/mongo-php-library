<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;

/**
 * Returns the document position (known as the rank) relative to other documents in the $setWindowFields stage partition.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rank/
 */
class RankOperator implements ResolvesToInt
{
    public const NAME = '$rank';
    public const ENCODE = Encode::Object;

    public function __construct()
    {
    }
}
