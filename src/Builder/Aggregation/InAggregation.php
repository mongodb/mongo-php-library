<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToBool;
use MongoDB\Model\BSONArray;

class InAggregation implements ResolvesToBool
{
    public const NAME = '$in';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param ExpressionInterface|mixed $expression Any valid expression expression. */
    public mixed $expression;

    /** @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $array Any valid expression that resolves to an array. */
    public PackedArray|ResolvesToArray|BSONArray|array $array;

    /**
     * @param ExpressionInterface|mixed $expression Any valid expression expression.
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $array Any valid expression that resolves to an array.
     */
    public function __construct(mixed $expression, PackedArray|ResolvesToArray|BSONArray|array $array)
    {
        $this->expression = $expression;
        if (\is_array($array) && ! \array_is_list($array)) {
            throw new \InvalidArgumentException('Expected $array argument to be a list, got an associative array.');
        }
        $this->array = $array;
    }
}
