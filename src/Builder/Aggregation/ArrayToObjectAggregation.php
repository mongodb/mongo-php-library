<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToObject;
use MongoDB\Model\BSONArray;

/**
 * Converts an array of key value pairs to a document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayToObject/
 */
class ArrayToObjectAggregation implements ResolvesToObject
{
    public const NAME = '$arrayToObject';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $array */
    public PackedArray|ResolvesToArray|BSONArray|array $array;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $array
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array $array)
    {
        if (\is_array($array) && ! \array_is_list($array)) {
            throw new \InvalidArgumentException('Expected $array argument to be a list, got an associative array.');
        }
        $this->array = $array;
    }
}
