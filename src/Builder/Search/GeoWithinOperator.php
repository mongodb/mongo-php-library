<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Search;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\GeometryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\SearchOperatorInterface;
use stdClass;

/**
 * The geoWithin operator supports querying geographic points within a given
 * geometry. Only points are returned, even if indexShapes value is true in
 * the index definition.
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/geoWithin/
 * @internal
 */
final class GeoWithinOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'geoWithin';

    public const PROPERTIES = [
        'path' => 'path',
        'box' => 'box',
        'circle' => 'circle',
        'geometry' => 'geometry',
        'score' => 'score',
    ];

    /** @var array|string $path */
    public readonly array|string $path;

    /** @var Optional|Document|Serializable|array|stdClass $box */
    public readonly Optional|Document|Serializable|stdClass|array $box;

    /** @var Optional|Document|Serializable|array|stdClass $circle */
    public readonly Optional|Document|Serializable|stdClass|array $circle;

    /** @var Optional|Document|GeometryInterface|Serializable|array|stdClass $geometry */
    public readonly Optional|Document|Serializable|GeometryInterface|stdClass|array $geometry;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param array|string $path
     * @param Optional|Document|Serializable|array|stdClass $box
     * @param Optional|Document|Serializable|array|stdClass $circle
     * @param Optional|Document|GeometryInterface|Serializable|array|stdClass $geometry
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public function __construct(
        array|string $path,
        Optional|Document|Serializable|stdClass|array $box = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $circle = Optional::Undefined,
        Optional|Document|Serializable|GeometryInterface|stdClass|array $geometry = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->path = $path;
        $this->box = $box;
        $this->circle = $circle;
        $this->geometry = $geometry;
        $this->score = $score;
    }
}
