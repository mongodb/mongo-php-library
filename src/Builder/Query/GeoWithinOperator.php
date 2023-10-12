<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\GeometryInterface;
use stdClass;

/**
 * Selects geometries within a bounding GeoJSON geometry. The 2dsphere and 2d indexes support $geoWithin.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoWithin/
 */
readonly class GeoWithinOperator implements FieldQueryInterface
{
    public const NAME = '$geoWithin';
    public const ENCODE = Encode::Single;

    /** @param Document|GeometryInterface|Serializable|array|stdClass $geometry */
    public Document|Serializable|GeometryInterface|stdClass|array $geometry;

    /**
     * @param Document|GeometryInterface|Serializable|array|stdClass $geometry
     */
    public function __construct(Document|Serializable|GeometryInterface|stdClass|array $geometry)
    {
        $this->geometry = $geometry;
    }
}
