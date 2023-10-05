<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Int64;
use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Model\BSONArray;

/**
 * Selects documents if a field is of the specified type.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/type/
 */
class TypeQuery implements QueryInterface
{
    public const NAME = '$type';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param BSONArray|Int64|PackedArray|int|list<ExpressionInterface|mixed>|non-empty-string $type */
    public Int64|PackedArray|BSONArray|array|int|string $type;

    /**
     * @param BSONArray|Int64|PackedArray|int|list<ExpressionInterface|mixed>|non-empty-string $type
     */
    public function __construct(Int64|PackedArray|BSONArray|array|int|string $type)
    {
        if (\is_array($type) && ! \array_is_list($type)) {
            throw new \InvalidArgumentException('Expected $type argument to be a list, got an associative array.');
        }
        $this->type = $type;
    }
}
