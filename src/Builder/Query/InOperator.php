<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Model\BSONArray;

/**
 * Matches any of the values specified in an array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/in/
 */
class InOperator implements QueryInterface
{
    public const NAME = '$in';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param BSONArray|PackedArray|array $value */
    public PackedArray|BSONArray|array $value;

    /**
     * @param BSONArray|PackedArray|array $value
     */
    public function __construct(PackedArray|BSONArray|array $value)
    {
        if (\is_array($value) && ! \array_is_list($value)) {
            throw new \InvalidArgumentException('Expected $value argument to be a list, got an associative array.');
        }

        $this->value = $value;
    }
}
