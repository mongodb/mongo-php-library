<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;

/**
 * Specifies a maximum distance to limit the results of $near and $nearSphere queries. The 2dsphere and 2d indexes support $maxDistance.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/maxDistance/
 */
readonly class MaxDistanceOperator implements FieldQueryInterface
{
    public const NAME = '$maxDistance';
    public const ENCODE = Encode::Single;

    /** @param Decimal128|Int64|float|int $value */
    public Decimal128|Int64|float|int $value;

    /**
     * @param Decimal128|Int64|float|int $value
     */
    public function __construct(Decimal128|Int64|float|int $value)
    {
        $this->value = $value;
    }
}
