<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\GeometryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;

/**
 * Specifies a geometry in GeoJSON format to geospatial query operators.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/geometry/
 * @internal
 */
final class GeometryOperator implements GeometryInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$geometry';
    public const PROPERTIES = ['type' => 'type', 'coordinates' => 'coordinates', 'crs' => 'crs'];

    /** @var string $type */
    public readonly string $type;

    /** @var BSONArray|PackedArray|array|string $coordinates */
    public readonly PackedArray|BSONArray|array|string $coordinates;

    /** @var Optional|Document|Serializable|array|stdClass|string $crs */
    public readonly Optional|Document|Serializable|stdClass|array|string $crs;

    /**
     * @param string $type
     * @param BSONArray|PackedArray|array|string $coordinates
     * @param Optional|Document|Serializable|array|stdClass|string $crs
     */
    public function __construct(
        string $type,
        PackedArray|BSONArray|array|string $coordinates,
        Optional|Document|Serializable|stdClass|array|string $crs = Optional::Undefined,
    ) {
        $this->type = $type;
        if (is_array($coordinates) && ! array_is_list($coordinates)) {
            throw new InvalidArgumentException('Expected $coordinates argument to be a list, got an associative array.');
        }

        $this->coordinates = $coordinates;
        $this->crs = $crs;
    }
}
