<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\QueryFilterInterface;

/**
 * Specifies a minimum distance to limit the results of $near and $nearSphere queries. For use with 2dsphere index only.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/minDistance/
 */
class MinDistanceOperator implements QueryFilterInterface
{
    public const NAME = '$minDistance';
    public const ENCODE = Encode::Single;

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
