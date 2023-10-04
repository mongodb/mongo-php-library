<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;

class GeoIntersectsQuery implements QueryInterface
{
    public const NAME = '$geoIntersects';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param array|object $geometry */
    public array|object $geometry;

    /**
     * @param array|object $geometry
     */
    public function __construct(array|object $geometry)
    {
        $this->geometry = $geometry;
    }
}
