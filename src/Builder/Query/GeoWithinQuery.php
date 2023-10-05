<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use stdClass;

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
