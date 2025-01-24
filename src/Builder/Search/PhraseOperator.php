<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Search;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\SearchOperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;

/**
 * The phrase operator performs search for documents containing an ordered sequence of terms using the analyzer specified in the index configuration.
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/phrase/
 * @internal
 */
final class PhraseOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'phrase';

    public const PROPERTIES = [
        'path' => 'path',
        'query' => 'query',
        'slop' => 'slop',
        'synonyms' => 'synonyms',
        'score' => 'score',
    ];

    /** @var array|string $path */
    public readonly array|string $path;

    /** @var BSONArray|PackedArray|array|string $query */
    public readonly PackedArray|BSONArray|array|string $query;

    /** @var Optional|int $slop */
    public readonly Optional|int $slop;

    /** @var Optional|string $synonyms */
    public readonly Optional|string $synonyms;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param array|string $path
     * @param BSONArray|PackedArray|array|string $query
     * @param Optional|int $slop
     * @param Optional|string $synonyms
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public function __construct(
        array|string $path,
        PackedArray|BSONArray|array|string $query,
        Optional|int $slop = Optional::Undefined,
        Optional|string $synonyms = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->path = $path;
        if (is_array($query) && ! array_is_list($query)) {
            throw new InvalidArgumentException('Expected $query argument to be a list, got an associative array.');
        }

        $this->query = $query;
        $this->slop = $slop;
        $this->synonyms = $synonyms;
        $this->score = $score;
    }
}
