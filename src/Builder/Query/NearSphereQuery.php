<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Type\QueryInterface;
use stdClass;

/**
 * Returns geospatial objects in proximity to a point on a sphere. Requires a geospatial index. The 2dsphere and 2d indexes support $nearSphere.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/nearSphere/
 */
class NearSphereQuery implements QueryInterface
{
    public const NAME = '$nearSphere';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Document|Serializable|array|stdClass $geometry */
    public Document|Serializable|stdClass|array $geometry;

    /** @param Optional|int $maxDistance Distance in meters. */
    public Optional|int $maxDistance;

    /** @param Optional|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point. */
    public Optional|int $minDistance;

    /**
     * @param Document|Serializable|array|stdClass $geometry
     * @param Optional|int $maxDistance Distance in meters.
     * @param Optional|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point.
     */
    public function __construct(
        Document|Serializable|stdClass|array $geometry,
        Optional|int $maxDistance = Optional::Undefined,
        Optional|int $minDistance = Optional::Undefined,
    ) {
        $this->geometry = $geometry;
        $this->maxDistance = $maxDistance;
        $this->minDistance = $minDistance;
    }
}
