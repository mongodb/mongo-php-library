<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Query\QueryInterface;

class GeoNearStage implements StageInterface
{
    public const NAME = '$geoNear';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param non-empty-string $distanceField The output field that contains the calculated distance. To specify a field within an embedded document, use dot notation. */
    public string $distanceField;

    /** @param array|object $near The point for which to find the closest documents. */
    public array|object $near;

    /** @param Decimal128|Int64|Optional|float|int $distanceMultiplier The factor to multiply all distances returned by the query. For example, use the distanceMultiplier to convert radians, as returned by a spherical query, to kilometers by multiplying by the radius of the Earth. */
    public Decimal128|Int64|Optional|float|int $distanceMultiplier;

    /** @param Optional|non-empty-string $includeLocs This specifies the output field that identifies the location used to calculate the distance. This option is useful when a location field contains multiple locations. To specify a field within an embedded document, use dot notation. */
    public Optional|string $includeLocs;

    /** @param Optional|non-empty-string $key Specify the geospatial indexed field to use when calculating the distance. */
    public Optional|string $key;

    /**
     * @param Decimal128|Int64|Optional|float|int $maxDistance The maximum distance from the center point that the documents can be. MongoDB limits the results to those documents that fall within the specified distance from the center point.
     * Specify the distance in meters if the specified point is GeoJSON and in radians if the specified point is legacy coordinate pairs.
     */
    public Decimal128|Int64|Optional|float|int $maxDistance;

    /**
     * @param Decimal128|Int64|Optional|float|int $minDistance The minimum distance from the center point that the documents can be. MongoDB limits the results to those documents that fall outside the specified distance from the center point.
     * Specify the distance in meters for GeoJSON data and in radians for legacy coordinate pairs.
     */
    public Decimal128|Int64|Optional|float|int $minDistance;

    /**
     * @param Optional|QueryInterface|array|object $query imits the results to the documents that match the query. The query syntax is the usual MongoDB read operation query syntax.
     * You cannot specify a $near predicate in the query field of the $geoNear stage.
     */
    public array|object $query;

    /**
     * @param Optional|bool $spherical Determines how MongoDB calculates the distance between two points:
     * - When true, MongoDB uses $nearSphere semantics and calculates distances using spherical geometry.
     * - When false, MongoDB uses $near semantics: spherical geometry for 2dsphere indexes and planar geometry for 2d indexes.
     * Default: false.
     */
    public Optional|bool $spherical;

    /**
     * @param non-empty-string $distanceField The output field that contains the calculated distance. To specify a field within an embedded document, use dot notation.
     * @param array|object $near The point for which to find the closest documents.
     * @param Decimal128|Int64|Optional|float|int $distanceMultiplier The factor to multiply all distances returned by the query. For example, use the distanceMultiplier to convert radians, as returned by a spherical query, to kilometers by multiplying by the radius of the Earth.
     * @param Optional|non-empty-string $includeLocs This specifies the output field that identifies the location used to calculate the distance. This option is useful when a location field contains multiple locations. To specify a field within an embedded document, use dot notation.
     * @param Optional|non-empty-string $key Specify the geospatial indexed field to use when calculating the distance.
     * @param Decimal128|Int64|Optional|float|int $maxDistance The maximum distance from the center point that the documents can be. MongoDB limits the results to those documents that fall within the specified distance from the center point.
     * Specify the distance in meters if the specified point is GeoJSON and in radians if the specified point is legacy coordinate pairs.
     * @param Decimal128|Int64|Optional|float|int $minDistance The minimum distance from the center point that the documents can be. MongoDB limits the results to those documents that fall outside the specified distance from the center point.
     * Specify the distance in meters for GeoJSON data and in radians for legacy coordinate pairs.
     * @param Optional|QueryInterface|array|object $query imits the results to the documents that match the query. The query syntax is the usual MongoDB read operation query syntax.
     * You cannot specify a $near predicate in the query field of the $geoNear stage.
     * @param Optional|bool $spherical Determines how MongoDB calculates the distance between two points:
     * - When true, MongoDB uses $nearSphere semantics and calculates distances using spherical geometry.
     * - When false, MongoDB uses $near semantics: spherical geometry for 2dsphere indexes and planar geometry for 2d indexes.
     * Default: false.
     */
    public function __construct(
        string $distanceField,
        array|object $near,
        Decimal128|Int64|Optional|float|int $distanceMultiplier = Optional::Undefined,
        Optional|string $includeLocs = Optional::Undefined,
        Optional|string $key = Optional::Undefined,
        Decimal128|Int64|Optional|float|int $maxDistance = Optional::Undefined,
        Decimal128|Int64|Optional|float|int $minDistance = Optional::Undefined,
        array|object $query = Optional::Undefined,
        Optional|bool $spherical = Optional::Undefined,
    ) {
        $this->distanceField = $distanceField;
        $this->near = $near;
        $this->distanceMultiplier = $distanceMultiplier;
        $this->includeLocs = $includeLocs;
        $this->key = $key;
        $this->maxDistance = $maxDistance;
        $this->minDistance = $minDistance;
        $this->query = $query;
        $this->spherical = $spherical;
    }
}
