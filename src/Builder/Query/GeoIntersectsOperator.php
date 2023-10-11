<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\GeometryInterface;
use MongoDB\Builder\Type\QueryFilterInterface;
use stdClass;

/**
 * Selects geometries that intersect with a GeoJSON geometry. The 2dsphere index supports $geoIntersects.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoIntersects/
 */
class GeoIntersectsOperator implements QueryFilterInterface
{
    public const NAME = '$geoIntersects';
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
