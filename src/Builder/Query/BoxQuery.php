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
 * Specifies a rectangular box using legacy coordinate pairs for $geoWithin queries. The 2d index supports $box.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/box/
 */
class BoxQuery implements QueryInterface
{
    public const NAME = '$box';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param BSONArray|PackedArray|list $value */
    public PackedArray|BSONArray|array $value;

    /**
     * @param BSONArray|PackedArray|list $value
     */
    public function __construct(PackedArray|BSONArray|array $value)
    {
        if (\is_array($value) && ! \array_is_list($value)) {
            throw new \InvalidArgumentException('Expected $value argument to be a list, got an associative array.');
        }
        $this->value = $value;
    }
}
