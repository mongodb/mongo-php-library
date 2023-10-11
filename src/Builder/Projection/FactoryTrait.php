<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Projection;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\QueryInterface;
use stdClass;

/**
 * @internal
 */
trait FactoryTrait
{
    /**
     * Projects the first element in an array that matches the specified $elemMatch condition.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/projection/elemMatch/
     * @param Document|QueryInterface|Serializable|array|stdClass $query
     */
    public static function elemMatch(Document|Serializable|QueryInterface|stdClass|array $query): ElemMatchOperator
    {
        return new ElemMatchOperator($query);
    }

    /**
     * Limits the number of elements projected from an array. Supports skip and limit slices.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/projection/slice/
     * @param int $limit
     * @param int $skip
     */
    public static function slice(int $limit, int $skip): SliceOperator
    {
        return new SliceOperator($limit, $skip);
    }
}
