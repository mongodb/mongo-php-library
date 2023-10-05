<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Model\BSONArray;

/**
 * Specifies a circle using legacy coordinate pairs to $geoWithin queries when using planar geometry. The 2d index supports $center.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/center/
 */
class CenterQuery implements QueryInterface
{
    public const NAME = '$center';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $value */
    public PackedArray|BSONArray|array $value;

    /**
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $value
     */
    public function __construct(PackedArray|BSONArray|array $value)
    {
        if (\is_array($value) && ! \array_is_list($value)) {
            throw new \InvalidArgumentException('Expected $value argument to be a list, got an associative array.');
        }
        $this->value = $value;
    }
}
