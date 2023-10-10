<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Model\BSONArray;
use stdClass;

/**
 * Specifies a geometry in GeoJSON format to geospatial query operators.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/geometry/
 */
class GeometryOperator implements QueryInterface
{
    public const NAME = '$geometry';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param non-empty-string $type */
    public string $type;

    /** @param BSONArray|PackedArray|array $coordinates */
    public PackedArray|BSONArray|array $coordinates;

    /** @param Document|Serializable|array|stdClass $crs */
    public Document|Serializable|stdClass|array $crs;

    /**
     * @param non-empty-string $type
     * @param BSONArray|PackedArray|array $coordinates
     * @param Document|Serializable|array|stdClass $crs
     */
    public function __construct(
        string $type,
        PackedArray|BSONArray|array $coordinates,
        Document|Serializable|stdClass|array $crs,
    ) {
        $this->type = $type;
        if (\is_array($coordinates) && ! \array_is_list($coordinates)) {
            throw new \InvalidArgumentException('Expected $coordinates argument to be a list, got an associative array.');
        }

        $this->coordinates = $coordinates;
        $this->crs = $crs;
    }
}
