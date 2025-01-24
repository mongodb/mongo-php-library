<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\GeometryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use stdClass;

/**
 * Returns geospatial objects in proximity to a point. Requires a geospatial index. The 2dsphere and 2d indexes support $near.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/near/
 * @internal
 */
final class NearOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$near';
    public const PROPERTIES = ['geometry' => null, 'maxDistance' => '$maxDistance', 'minDistance' => '$minDistance'];

    /** @var Document|GeometryInterface|Serializable|array|stdClass $geometry */
    public readonly Document|Serializable|GeometryInterface|stdClass|array $geometry;

    /** @var Optional|Decimal128|Int64|float|int $maxDistance Distance in meters. Limits the results to those documents that are at most the specified distance from the center point. */
    public readonly Optional|Decimal128|Int64|float|int $maxDistance;

    /** @var Optional|Decimal128|Int64|float|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point. */
    public readonly Optional|Decimal128|Int64|float|int $minDistance;

    /**
     * @param Document|GeometryInterface|Serializable|array|stdClass $geometry
     * @param Optional|Decimal128|Int64|float|int $maxDistance Distance in meters. Limits the results to those documents that are at most the specified distance from the center point.
     * @param Optional|Decimal128|Int64|float|int $minDistance Distance in meters. Limits the results to those documents that are at least the specified distance from the center point.
     */
    public function __construct(
        Document|Serializable|GeometryInterface|stdClass|array $geometry,
        Optional|Decimal128|Int64|float|int $maxDistance = Optional::Undefined,
        Optional|Decimal128|Int64|float|int $minDistance = Optional::Undefined,
    ) {
        $this->geometry = $geometry;
        $this->maxDistance = $maxDistance;
        $this->minDistance = $minDistance;
    }
}
