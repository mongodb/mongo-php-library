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
use MongoDB\Builder\Optional;
use MongoDB\Model\BSONArray;

class FilterAggregation implements ResolvesToArray
{
    public const NAME = '$filter';
    public const ENCODE = 'object';

    public PackedArray|ResolvesToArray|BSONArray|array $input;
    public ResolvesToBool|bool $cond;
    public Optional|ResolvesToString|string $as;
    public Optional|Int64|ResolvesToInt|int $limit;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> $input
     * @param ResolvesToBool|bool $cond
     * @param Optional|ResolvesToString|non-empty-string $as
     * @param Int64|Optional|ResolvesToInt|int $limit
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array $input,
        ResolvesToBool|bool $cond,
        Optional|ResolvesToString|string $as = Optional::Undefined,
        Optional|Int64|ResolvesToInt|int $limit = Optional::Undefined,
    ) {
        if (\is_array($input) && ! \array_is_list($input)) {
            throw new \InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }
        $this->input = $input;
        $this->cond = $cond;
        $this->as = $as;
        $this->limit = $limit;
    }
}
