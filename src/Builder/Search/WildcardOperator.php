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
 * The wildcard operator enables queries which use special characters in the search string that can match any character.
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/wildcard/
 * @internal
 */
final class WildcardOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'wildcard';

    public const PROPERTIES = [
        'path' => 'path',
        'query' => 'query',
        'allowAnalyzedField' => 'allowAnalyzedField',
        'score' => 'score',
    ];

    /** @var array|string $path */
    public readonly array|string $path;

    /** @var string $query */
    public readonly string $query;

    /** @var Optional|bool $allowAnalyzedField */
    public readonly Optional|bool $allowAnalyzedField;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param array|string $path
     * @param string $query
     * @param Optional|bool $allowAnalyzedField
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public function __construct(
        array|string $path,
        string $query,
        Optional|bool $allowAnalyzedField = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->path = $path;
        $this->query = $query;
        $this->allowAnalyzedField = $allowAnalyzedField;
        $this->score = $score;
    }
}
