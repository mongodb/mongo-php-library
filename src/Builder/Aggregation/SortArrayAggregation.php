<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Sorts the elements of an array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/
 */
class SortArrayAggregation implements ResolvesToArray
{
    public const NAME = '$sortArray';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param BSONArray|PackedArray|ResolvesToArray|list $input The array to be sorted. */
    public PackedArray|ResolvesToArray|BSONArray|array $input;

    /** @param array|stdClass $sortBy The document specifies a sort ordering. */
    public stdClass|array $sortBy;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list $input The array to be sorted.
     * @param array|stdClass $sortBy The document specifies a sort ordering.
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array $input, stdClass|array $sortBy)
    {
        if (\is_array($input) && ! \array_is_list($input)) {
            throw new \InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }
        $this->input = $input;
        $this->sortBy = $sortBy;
    }
}
