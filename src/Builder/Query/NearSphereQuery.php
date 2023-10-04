<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;

class NearSphereQuery implements QueryInterface
{
    public const NAME = '$nearSphere';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param array|object $geometry */
    public array|object $geometry;

    /** @param Int64|Optional|int $maxDistance Distance in meters. */
    public Int64|Optional|int $maxDistance;

    /** @param Int64|Optional|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point. */
    public Int64|Optional|int $minDistance;

    /**
     * @param array|object $geometry
     * @param Int64|Optional|int $maxDistance Distance in meters.
     * @param Int64|Optional|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point.
     */
    public function __construct(
        array|object $geometry,
        Int64|Optional|int $maxDistance = Optional::Undefined,
        Int64|Optional|int $minDistance = Optional::Undefined,
    ) {
        $this->geometry = $geometry;
        $this->maxDistance = $maxDistance;
        $this->minDistance = $minDistance;
    }
}
