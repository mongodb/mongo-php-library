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

class ConcatArraysAggregation implements ResolvesToArray
{
    public const NAME = '$concatArrays';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /**
     * @no-named-arguments
     * @param list<BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed>> ...$array
     */
    public array $array;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> ...$array
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array ...$array)
    {
        if (\count($array) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $array, got %d.', 1, \count($array)));
        }
        if (! \array_is_list($array)) {
            throw new \InvalidArgumentException('Expected $array arguments to be a list of BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed>, named arguments are not supported');
        }
        $this->array = $array;
    }
}
