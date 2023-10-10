<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;

/**
 * Converts an array of key value pairs to a document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayToObject/
 */
class ArrayToObjectOperator implements ResolvesToObject
{
    public const NAME = '$arrayToObject';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param BSONArray|PackedArray|ResolvesToArray|array $array */
    public PackedArray|ResolvesToArray|BSONArray|array $array;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array $array
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array $array)
    {
        if (is_array($array) && ! array_is_list($array)) {
            throw new InvalidArgumentException('Expected $array argument to be a list, got an associative array.');
        }

        $this->array = $array;
    }
}
