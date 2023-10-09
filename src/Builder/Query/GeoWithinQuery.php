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
 * Selects geometries within a bounding GeoJSON geometry. The 2dsphere and 2d indexes support $geoWithin.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoWithin/
 */
class GeoWithinQuery implements QueryInterface
{
    public const NAME = '$geoWithin';
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
