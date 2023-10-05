<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;
use stdClass;

/**
 * Returns geospatial objects in proximity to a point. Requires a geospatial index. The 2dsphere and 2d indexes support $near.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/near/
 */
class NearQuery implements QueryInterface
{
    public const NAME = '$near';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param array|stdClass $geometry */
    public stdClass|array $geometry;

    /** @param Int64|Optional|int $maxDistance Distance in meters. Limits the results to those documents that are at most the specified distance from the center point. */
    public Int64|Optional|int $maxDistance;

    /** @param Int64|Optional|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point. */
    public Int64|Optional|int $minDistance;

    /**
     * @param array|stdClass $geometry
     * @param Int64|Optional|int $maxDistance Distance in meters. Limits the results to those documents that are at most the specified distance from the center point.
     * @param Int64|Optional|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point.
     */
    public function __construct(
        stdClass|array $geometry,
        Int64|Optional|int $maxDistance = Optional::Undefined,
        Int64|Optional|int $minDistance = Optional::Undefined,
    ) {
        $this->geometry = $geometry;
        $this->maxDistance = $maxDistance;
        $this->minDistance = $minDistance;
    }
}
