<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\QueryInterface;

/**
 * Specifies a minimum distance to limit the results of $near and $nearSphere queries. For use with 2dsphere index only.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/minDistance/
 */
class MinDistanceQuery implements QueryInterface
{
    public const NAME = '$minDistance';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Int64|float|int $value */
    public Int64|float|int $value;

    /**
     * @param Int64|float|int $value
     */
    public function __construct(Int64|float|int $value)
    {
        $this->value = $value;
    }
}
