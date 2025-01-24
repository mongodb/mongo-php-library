<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Search;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\SearchOperatorInterface;
use stdClass;

/**
 * The facet collector groups results by values or ranges in the specified
 * faceted fields and returns the count for each of those groups.
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/facet/
 * @internal
 */
final class FacetOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'facet';
    public const PROPERTIES = ['facets' => 'facets', 'operator' => 'operator'];

    /** @var Document|Serializable|array|stdClass $facets */
    public readonly Document|Serializable|stdClass|array $facets;

    /** @var Optional|Document|SearchOperatorInterface|Serializable|array|stdClass $operator */
    public readonly Optional|Document|Serializable|SearchOperatorInterface|stdClass|array $operator;

    /**
     * @param Document|Serializable|array|stdClass $facets
     * @param Optional|Document|SearchOperatorInterface|Serializable|array|stdClass $operator
     */
    public function __construct(
        Document|Serializable|stdClass|array $facets,
        Optional|Document|Serializable|SearchOperatorInterface|stdClass|array $operator = Optional::Undefined,
    ) {
        $this->facets = $facets;
        $this->operator = $operator;
    }
}
