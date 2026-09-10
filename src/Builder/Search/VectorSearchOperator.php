<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Search;

use MongoDB\BSON\Binary;
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
 * The vectorSearch operator performs an ANN or ENN search on a vector field. It can only be
 * used as a top-level operator in a $search or $searchMeta query, not nested under compound
 * or other operators.
 *
 * New in MongoDB 6.0
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/vector-search/
 * @internal
 */
final class VectorSearchOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'vectorSearch';

    public const PROPERTIES = [
        'path' => 'path',
        'queryVector' => 'queryVector',
        'limit' => 'limit',
        'exact' => 'exact',
        'numCandidates' => 'numCandidates',
        'filter' => 'filter',
        'score' => 'score',
    ];

    /** @var array|string $path The indexed vector field to search. */
    public readonly array|string $path;

    /**
     * @var BSONArray|Binary|PackedArray|array|string $queryVector Array of numbers or a BinData value that represents the query vector. The number type
     * must match the indexed field value type.
     */
    public readonly Binary|PackedArray|BSONArray|array|string $queryVector;

    /**
     * @var int $limit The integer number of documents to return in the results. This value cannot exceed
     * numCandidates if numCandidates is specified.
     */
    public readonly int $limit;

    /**
     * @var Optional|bool $exact If false, runs an ANN search. If true, runs an ENN search. Defaults to false.
     * This parameter is required if numCandidates is omitted.
     */
    public readonly Optional|bool $exact;

    /**
     * @var Optional|int $numCandidates The number of nearest neighbors to use during the search. Value must be less than or
     * equal to 10000 and cannot be less than limit. This field is required if exact is false
     * or omitted.
     */
    public readonly Optional|int $numCandidates;

    /** @var Optional|Document|SearchOperatorInterface|Serializable|array|stdClass $filter Any Atlas Search operator to filter documents based on metadata or specific search criteria. */
    public readonly Optional|Document|Serializable|SearchOperatorInterface|stdClass|array $filter;

    /** @var Optional|Document|Serializable|array|stdClass $score Score assigned to matching search results. */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param array|string $path The indexed vector field to search.
     * @param BSONArray|Binary|PackedArray|array|string $queryVector Array of numbers or a BinData value that represents the query vector. The number type
     * must match the indexed field value type.
     * @param int $limit The integer number of documents to return in the results. This value cannot exceed
     * numCandidates if numCandidates is specified.
     * @param Optional|bool $exact If false, runs an ANN search. If true, runs an ENN search. Defaults to false.
     * This parameter is required if numCandidates is omitted.
     * @param Optional|int $numCandidates The number of nearest neighbors to use during the search. Value must be less than or
     * equal to 10000 and cannot be less than limit. This field is required if exact is false
     * or omitted.
     * @param Optional|Document|SearchOperatorInterface|Serializable|array|stdClass $filter Any Atlas Search operator to filter documents based on metadata or specific search criteria.
     * @param Optional|Document|Serializable|array|stdClass $score Score assigned to matching search results.
     */
    public function __construct(
        array|string $path,
        Binary|PackedArray|BSONArray|array|string $queryVector,
        int $limit,
        Optional|bool $exact = Optional::Undefined,
        Optional|int $numCandidates = Optional::Undefined,
        Optional|Document|Serializable|SearchOperatorInterface|stdClass|array $filter = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->path = $path;
        if (is_array($queryVector) && ! array_is_list($queryVector)) {
            throw new InvalidArgumentException('Expected $queryVector argument to be a list, got an associative array.');
        }

        $this->queryVector = $queryVector;
        $this->limit = $limit;
        $this->exact = $exact;
        $this->numCandidates = $numCandidates;
        $this->filter = $filter;
        $this->score = $score;
    }
}
