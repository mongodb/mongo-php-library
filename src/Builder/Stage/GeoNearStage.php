<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Expression\ResolvesToObject;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_array;
use function is_string;
use function str_starts_with;

/**
 * Returns an ordered stream of documents based on the proximity to a geospatial point. Incorporates the functionality of $match, $sort, and $limit for geospatial data. The output documents include an additional distance field and can include a location identifier field.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/geoNear/
 * @internal
 */
final class GeoNearStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$geoNear';

    public const PROPERTIES = [
        'near' => 'near',
        'distanceField' => 'distanceField',
        'distanceMultiplier' => 'distanceMultiplier',
        'includeLocs' => 'includeLocs',
        'key' => 'key',
        'maxDistance' => 'maxDistance',
        'minDistance' => 'minDistance',
        'query' => 'query',
        'spherical' => 'spherical',
    ];

    /** @var Document|ResolvesToObject|Serializable|array|stdClass|string $near The point for which to find the closest documents. */
    public readonly Document|Serializable|ResolvesToObject|stdClass|array|string $near;

    /** @var Optional|string $distanceField The output field that contains the calculated distance. To specify a field within an embedded document, use dot notation. */
    public readonly Optional|string $distanceField;

    /** @var Optional|Decimal128|Int64|float|int $distanceMultiplier The factor to multiply all distances returned by the query. For example, use the distanceMultiplier to convert radians, as returned by a spherical query, to kilometers by multiplying by the radius of the Earth. */
    public readonly Optional|Decimal128|Int64|float|int $distanceMultiplier;

    /** @var Optional|string $includeLocs This specifies the output field that identifies the location used to calculate the distance. This option is useful when a location field contains multiple locations. To specify a field within an embedded document, use dot notation. */
    public readonly Optional|string $includeLocs;

    /** @var Optional|string $key Specify the geospatial indexed field to use when calculating the distance. */
    public readonly Optional|string $key;

    /**
     * @var Optional|Decimal128|Int64|float|int $maxDistance The maximum distance from the center point that the documents can be. MongoDB limits the results to those documents that fall within the specified distance from the center point.
     * Specify the distance in meters if the specified point is GeoJSON and in radians if the specified point is legacy coordinate pairs.
     */
    public readonly Optional|Decimal128|Int64|float|int $maxDistance;

    /**
     * @var Optional|Decimal128|Int64|float|int $minDistance The minimum distance from the center point that the documents can be. MongoDB limits the results to those documents that fall outside the specified distance from the center point.
     * Specify the distance in meters for GeoJSON data and in radians for legacy coordinate pairs.
     */
    public readonly Optional|Decimal128|Int64|float|int $minDistance;

    /**
     * @var Optional|QueryInterface|array $query Limits the results to the documents that match the query. The query syntax is the usual MongoDB read operation query syntax.
     * You cannot specify a $near predicate in the query field of the $geoNear stage.
     */
    public readonly Optional|QueryInterface|array $query;

    /**
     * @var Optional|bool $spherical Determines how MongoDB calculates the distance between two points:
     * - When true, MongoDB uses $nearSphere semantics and calculates distances using spherical geometry.
     * - When false, MongoDB uses $near semantics: spherical geometry for 2dsphere indexes and planar geometry for 2d indexes.
     * Default: false.
     */
    public readonly Optional|bool $spherical;

    /**
     * @param Document|ResolvesToObject|Serializable|array|stdClass|string $near The point for which to find the closest documents.
     * @param Optional|string $distanceField The output field that contains the calculated distance. To specify a field within an embedded document, use dot notation.
     * @param Optional|Decimal128|Int64|float|int $distanceMultiplier The factor to multiply all distances returned by the query. For example, use the distanceMultiplier to convert radians, as returned by a spherical query, to kilometers by multiplying by the radius of the Earth.
     * @param Optional|string $includeLocs This specifies the output field that identifies the location used to calculate the distance. This option is useful when a location field contains multiple locations. To specify a field within an embedded document, use dot notation.
     * @param Optional|string $key Specify the geospatial indexed field to use when calculating the distance.
     * @param Optional|Decimal128|Int64|float|int $maxDistance The maximum distance from the center point that the documents can be. MongoDB limits the results to those documents that fall within the specified distance from the center point.
     * Specify the distance in meters if the specified point is GeoJSON and in radians if the specified point is legacy coordinate pairs.
     * @param Optional|Decimal128|Int64|float|int $minDistance The minimum distance from the center point that the documents can be. MongoDB limits the results to those documents that fall outside the specified distance from the center point.
     * Specify the distance in meters for GeoJSON data and in radians for legacy coordinate pairs.
     * @param Optional|QueryInterface|array $query Limits the results to the documents that match the query. The query syntax is the usual MongoDB read operation query syntax.
     * You cannot specify a $near predicate in the query field of the $geoNear stage.
     * @param Optional|bool $spherical Determines how MongoDB calculates the distance between two points:
     * - When true, MongoDB uses $nearSphere semantics and calculates distances using spherical geometry.
     * - When false, MongoDB uses $near semantics: spherical geometry for 2dsphere indexes and planar geometry for 2d indexes.
     * Default: false.
     */
    public function __construct(
        Document|Serializable|ResolvesToObject|stdClass|array|string $near,
        Optional|string $distanceField = Optional::Undefined,
        Optional|Decimal128|Int64|float|int $distanceMultiplier = Optional::Undefined,
        Optional|string $includeLocs = Optional::Undefined,
        Optional|string $key = Optional::Undefined,
        Optional|Decimal128|Int64|float|int $maxDistance = Optional::Undefined,
        Optional|Decimal128|Int64|float|int $minDistance = Optional::Undefined,
        Optional|QueryInterface|array $query = Optional::Undefined,
        Optional|bool $spherical = Optional::Undefined,
    ) {
        if (is_string($near) && ! str_starts_with($near, '$')) {
            throw new InvalidArgumentException('Argument $near can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->near = $near;
        $this->distanceField = $distanceField;
        $this->distanceMultiplier = $distanceMultiplier;
        $this->includeLocs = $includeLocs;
        $this->key = $key;
        $this->maxDistance = $maxDistance;
        $this->minDistance = $minDistance;
        if (is_array($query)) {
            $query = QueryObject::create($query);
        }

        $this->query = $query;
        $this->spherical = $spherical;
    }
}
