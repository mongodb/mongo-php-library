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
 * The geoShape operator supports querying shapes with a relation to a given
 * geometry if indexShapes is set to true in the index definition.
 *
 * New in MongoDB 5.0
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/geoShape/
 * @internal
 */
final class GeoShapeOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'geoShape';
    public const PROPERTIES = ['path' => 'path', 'relation' => 'relation', 'geometry' => 'geometry', 'score' => 'score'];

    /** @var array|string $path */
    public readonly array|string $path;

    /** @var string $relation */
    public readonly string $relation;

    /** @var Document|GeometryInterface|Serializable|array|stdClass $geometry */
    public readonly Document|Serializable|GeometryInterface|stdClass|array $geometry;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param array|string $path
     * @param string $relation
     * @param Document|GeometryInterface|Serializable|array|stdClass $geometry
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public function __construct(
        array|string $path,
        string $relation,
        Document|Serializable|GeometryInterface|stdClass|array $geometry,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->path = $path;
        $this->relation = $relation;
        $this->geometry = $geometry;
        $this->score = $score;
    }
}
