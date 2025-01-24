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
 * The autocomplete operator performs a search for a word or phrase that
 * contains a sequence of characters from an incomplete input string. The
 * fields that you intend to query with the autocomplete operator must be
 * indexed with the autocomplete data type in the collection's index definition.
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/autocomplete/
 * @internal
 */
final class AutocompleteOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'autocomplete';

    public const PROPERTIES = [
        'path' => 'path',
        'query' => 'query',
        'tokenOrder' => 'tokenOrder',
        'fuzzy' => 'fuzzy',
        'score' => 'score',
    ];

    /** @var array|string $path */
    public readonly array|string $path;

    /** @var string $query */
    public readonly string $query;

    /** @var Optional|string $tokenOrder */
    public readonly Optional|string $tokenOrder;

    /** @var Optional|Document|Serializable|array|stdClass $fuzzy */
    public readonly Optional|Document|Serializable|stdClass|array $fuzzy;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param array|string $path
     * @param string $query
     * @param Optional|string $tokenOrder
     * @param Optional|Document|Serializable|array|stdClass $fuzzy
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public function __construct(
        array|string $path,
        string $query,
        Optional|string $tokenOrder = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $fuzzy = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->path = $path;
        $this->query = $query;
        $this->tokenOrder = $tokenOrder;
        $this->fuzzy = $fuzzy;
        $this->score = $score;
    }
}
