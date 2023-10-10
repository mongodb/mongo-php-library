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
 * Specifies a circle using either legacy coordinate pairs or GeoJSON format for $geoWithin queries when using spherical geometry. The 2dsphere and 2d indexes support $centerSphere.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/centerSphere/
 */
class CenterSphereOperator implements QueryInterface
{
    public const NAME = '$centerSphere';
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
