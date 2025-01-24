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
 * The text operator performs a full-text search using the analyzer that you specify in the index configuration.
 * If you omit an analyzer, the text operator uses the default standard analyzer.
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/text/
 * @internal
 */
final class TextOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'text';

    public const PROPERTIES = [
        'path' => 'path',
        'query' => 'query',
        'fuzzy' => 'fuzzy',
        'matchCriteria' => 'matchCriteria',
        'synonyms' => 'synonyms',
        'score' => 'score',
    ];

    /** @var array|string $path */
    public readonly array|string $path;

    /** @var string $query */
    public readonly string $query;

    /** @var Optional|Document|Serializable|array|stdClass $fuzzy */
    public readonly Optional|Document|Serializable|stdClass|array $fuzzy;

    /** @var Optional|string $matchCriteria */
    public readonly Optional|string $matchCriteria;

    /** @var Optional|string $synonyms */
    public readonly Optional|string $synonyms;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param array|string $path
     * @param string $query
     * @param Optional|Document|Serializable|array|stdClass $fuzzy
     * @param Optional|string $matchCriteria
     * @param Optional|string $synonyms
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public function __construct(
        array|string $path,
        string $query,
        Optional|Document|Serializable|stdClass|array $fuzzy = Optional::Undefined,
        Optional|string $matchCriteria = Optional::Undefined,
        Optional|string $synonyms = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->path = $path;
        $this->query = $query;
        $this->fuzzy = $fuzzy;
        $this->matchCriteria = $matchCriteria;
        $this->synonyms = $synonyms;
        $this->score = $score;
    }
}
