<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use InvalidArgumentException;
use MongoDB\BSON\Int64;
use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Expression\Expression;
use MongoDB\Builder\Expression\FieldPath;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToBool;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;
use function sprintf;

class FilterAggregation implements ResolvesToArray
{
    public const NAME = '$filter';
    public const ENCODE = 'object';

    public PackedArray|FieldPath|ResolvesToArray|BSONArray|array|string $input;
    public ResolvesToBool|bool $cond;
    public ResolvesToString|null|string $as;
    public Int64|ResolvesToInt|int|null $limit;

    /**
     * @param BSONArray|FieldPath|PackedArray|ResolvesToArray|list<Expression|mixed>|string $input
     * @param ResolvesToString|string|null                                                  $as
     * @param Int64|ResolvesToInt|int|null                                                  $limit
     */
    public function __construct(
        PackedArray|FieldPath|ResolvesToArray|BSONArray|array|string $input,
        ResolvesToBool|bool $cond,
        ResolvesToString|null|string $as = null,
        Int64|ResolvesToInt|int|null $limit = null,
    ) {
        if (is_array($input) && ! array_is_list($input)) {
            throw new InvalidArgumentException(sprintf('Expected $input argument to be a list, got an associative array.'));
        }

        $this->input = $input;
        $this->cond = $cond;
        $this->as = $as;
        $this->limit = $limit;
    }
}
