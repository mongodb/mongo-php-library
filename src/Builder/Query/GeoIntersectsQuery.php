<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\QueryInterface;
use stdClass;

/**
 * Selects geometries that intersect with a GeoJSON geometry. The 2dsphere index supports $geoIntersects.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoIntersects/
 */
class GeoIntersectsQuery implements QueryInterface
{
    public const NAME = '$geoIntersects';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|Serializable|array|stdClass $geometry */
    public Document|Serializable|stdClass|array $geometry;

    /**
     * @param Document|Serializable|array|stdClass $geometry
     */
    public function __construct(Document|Serializable|stdClass|array $geometry)
    {
        $this->geometry = $geometry;
    }
}
