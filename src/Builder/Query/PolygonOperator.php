<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\GeometryInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;

/**
 * Specifies a polygon to using legacy coordinate pairs for $geoWithin queries. The 2d index supports $center.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/polygon/
 */
readonly class PolygonOperator implements GeometryInterface
{
    public const NAME = '$polygon';
    public const ENCODE = Encode::Single;

    /** @param BSONArray|PackedArray|array $points */
    public PackedArray|BSONArray|array $points;

    /**
     * @param BSONArray|PackedArray|array $points
     */
    public function __construct(PackedArray|BSONArray|array $points)
    {
        if (is_array($points) && ! array_is_list($points)) {
            throw new InvalidArgumentException('Expected $points argument to be a list, got an associative array.');
        }

        $this->points = $points;
    }
}
