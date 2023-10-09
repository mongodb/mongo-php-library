<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDouble;

/**
 * Returns a random float between 0 and 1
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rand/
 */
class RandAggregation implements ResolvesToDouble
{
    public const NAME = '$rand';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    public function __construct()
    {
    }
}
