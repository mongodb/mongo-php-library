<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use stdClass;

/**
 * Selects geometries within a bounding GeoJSON geometry. The 2dsphere and 2d indexes support $geoWithin.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoWithin/
 */
class GeoWithinQuery implements QueryInterface
{
    public const NAME = '$geoWithin';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param array|stdClass $geometry */
    public stdClass|array $geometry;

    /**
     * @param array|stdClass $geometry
     */
    public function __construct(stdClass|array $geometry)
    {
        $this->geometry = $geometry;
    }
}
