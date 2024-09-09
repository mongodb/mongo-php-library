<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\GeometryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;

/**
 * Specifies a polygon to using legacy coordinate pairs for $geoWithin queries. The 2d index supports $center.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/polygon/
 */
class PolygonOperator implements GeometryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var BSONArray|PackedArray|array $points */
    public readonly PackedArray|BSONArray|array $points;

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

    public function getOperator(): string
    {
        return '$polygon';
    }
}
