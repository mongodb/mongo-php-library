<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Int64;
use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToBool;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Model\BSONArray;

class FilterAggregation implements ResolvesToArray
{
    public const NAME = '$filter';
    public const ENCODE = 'object';

    public PackedArray|ResolvesToArray|BSONArray|array $input;
    public ResolvesToBool|bool $cond;
    public ResolvesToString|null|string $as;
    public Int64|ResolvesToInt|int|null $limit;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<Expression|mixed> $input
     * @param ResolvesToBool|bool $cond
     * @param ResolvesToString|null|string $as
     * @param Int64|ResolvesToInt|int|null $limit
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        ResolvesToBool|bool $cond,
        ResolvesToString|null|string $as = null,
        Int64|ResolvesToInt|int|null $limit = null,
    ) {
        if (\is_array($input) && ! \array_is_list($input)) {
            throw new \InvalidArgumentException(\sprintf('Expected $input argument to be a list, got an associative array.'));
        }
        $this->input = $input;
        $this->cond = $cond;
        $this->as = $as;
        $this->limit = $limit;
    }
}
