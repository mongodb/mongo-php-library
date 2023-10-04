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

class SetIsSubsetAggregation implements ResolvesToBool
{
    public const NAME = '$setIsSubset';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression1 */
    public PackedArray|ResolvesToArray|BSONArray|array $expression1;

    /** @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression2 */
    public PackedArray|ResolvesToArray|BSONArray|array $expression2;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression1
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $expression2
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array $expression1,
        PackedArray|ResolvesToArray|BSONArray|array $expression2,
    ) {
        if (\is_array($expression1) && ! \array_is_list($expression1)) {
            throw new \InvalidArgumentException('Expected $expression1 argument to be a list, got an associative array.');
        }
        $this->expression1 = $expression1;
        if (\is_array($expression2) && ! \array_is_list($expression2)) {
            throw new \InvalidArgumentException('Expected $expression2 argument to be a list, got an associative array.');
        }
        $this->expression2 = $expression2;
    }
}
