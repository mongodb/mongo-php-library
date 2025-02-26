<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Search;

use DateTimeInterface;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\GeometryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\SearchOperatorInterface;
use stdClass;

/**
 * The near operator supports querying and scoring numeric, date, and GeoJSON point values.
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/near/
 * @internal
 */
final class NearOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'near';
    public const PROPERTIES = ['path' => 'path', 'origin' => 'origin', 'pivot' => 'pivot', 'score' => 'score'];

    /** @var array|string $path */
    public readonly array|string $path;

    /** @var DateTimeInterface|Decimal128|Document|GeometryInterface|Int64|Serializable|UTCDateTime|array|float|int|stdClass $origin */
    public readonly DateTimeInterface|Decimal128|Document|Int64|Serializable|UTCDateTime|GeometryInterface|stdClass|array|float|int $origin;

    /** @var Decimal128|Int64|float|int $pivot */
    public readonly Decimal128|Int64|float|int $pivot;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param array|string $path
     * @param DateTimeInterface|Decimal128|Document|GeometryInterface|Int64|Serializable|UTCDateTime|array|float|int|stdClass $origin
     * @param Decimal128|Int64|float|int $pivot
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public function __construct(
        array|string $path,
        DateTimeInterface|Decimal128|Document|Int64|Serializable|UTCDateTime|GeometryInterface|stdClass|array|float|int $origin,
        Decimal128|Int64|float|int $pivot,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->path = $path;
        $this->origin = $origin;
        $this->pivot = $pivot;
        $this->score = $score;
    }
}
