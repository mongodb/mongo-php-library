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
 * Specifies a polygon to using legacy coordinate pairs for $geoWithin queries. The 2d index supports $center.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/polygon/
 */
class PolygonQuery implements QueryInterface
{
    public const NAME = '$polygon';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $points */
    public PackedArray|BSONArray|array $points;

    /**
     * @param BSONArray|PackedArray|list<ExpressionInterface|mixed> $points
     */
    public function __construct(PackedArray|BSONArray|array $points)
    {
        if (\is_array($points) && ! \array_is_list($points)) {
            throw new \InvalidArgumentException('Expected $points argument to be a list, got an associative array.');
        }
        $this->points = $points;
    }
}
