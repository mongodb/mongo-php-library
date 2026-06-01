<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\InputStageInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;

/**
 * The $vectorSearch stage performs an ANN or ENN search on a vector in the specified field.
 *
 * New in MongoDB 6.0
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-vector-search/vector-search-stage/
 * @internal
 */
final class VectorSearchStage implements InputStageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$vectorSearch';

    public const PROPERTIES = [
        'index' => 'index',
        'limit' => 'limit',
        'path' => 'path',
        'queryVector' => 'queryVector',
        'exact' => 'exact',
        'filter' => 'filter',
        'numCandidates' => 'numCandidates',
        'returnStoredSource' => 'returnStoredSource',
        'nestedOptions' => 'nestedOptions',
        'parentFilter' => 'parentFilter',
    ];

    /** @var string $index Name of the Atlas Vector Search index to use. */
    public readonly string $index;

    /** @var int $limit Number of documents to return in the results. This value can't exceed the value of numCandidates if you specify numCandidates. */
    public readonly int $limit;

    /** @var string $path Indexed vector type field to search. */
    public readonly string $path;

    /**
     * @var BSONArray|Binary|PackedArray|array|string $queryVector Array of numbers or a BinData value that represent the query vector. The number type
     * must match the indexed field value type.
     */
    public readonly Binary|PackedArray|BSONArray|array|string $queryVector;

    /** @var Optional|bool $exact This is required if numCandidates is omitted. false to run ANN search. true to run ENN search. */
    public readonly Optional|bool $exact;

    /** @var Optional|QueryInterface|array $filter Any match query that compares an indexed field with a boolean, date, objectId, number, string, or UUID to use as a pre-filter. */
    public readonly Optional|QueryInterface|array $filter;

    /**
     * @var Optional|int $numCandidates This field is required if exact is false or omitted.
     * Number of nearest neighbors to use during the search. Value must be less than or equal to (<=) 10000. You can't specify a number less than the number of documents to return (limit).
     */
    public readonly Optional|int $numCandidates;

    /** @var Optional|bool $returnStoredSource If true, the search returns only the stored source fields configured on the index directly from the index and skips a full document lookup. If omitted, the default value is false. */
    public readonly Optional|bool $returnStoredSource;

    /** @var Optional|Document|Serializable|array|stdClass $nestedOptions Configure how MongoDB Vector Search scores documents that contain nested arrays. */
    public readonly Optional|Document|Serializable|stdClass|array $nestedOptions;

    /** @var Optional|QueryInterface|array $parentFilter Any match query that compares an indexed top-level field with a boolean, date, objectId, number, string, or UUID to use as a pre-filter. Only valid if `nestedRoot` is specified in the index definition. */
    public readonly Optional|QueryInterface|array $parentFilter;

    /**
     * @param string $index Name of the Atlas Vector Search index to use.
     * @param int $limit Number of documents to return in the results. This value can't exceed the value of numCandidates if you specify numCandidates.
     * @param string $path Indexed vector type field to search.
     * @param BSONArray|Binary|PackedArray|array|string $queryVector Array of numbers or a BinData value that represent the query vector. The number type
     * must match the indexed field value type.
     * @param Optional|bool $exact This is required if numCandidates is omitted. false to run ANN search. true to run ENN search.
     * @param Optional|QueryInterface|array $filter Any match query that compares an indexed field with a boolean, date, objectId, number, string, or UUID to use as a pre-filter.
     * @param Optional|int $numCandidates This field is required if exact is false or omitted.
     * Number of nearest neighbors to use during the search. Value must be less than or equal to (<=) 10000. You can't specify a number less than the number of documents to return (limit).
     * @param Optional|bool $returnStoredSource If true, the search returns only the stored source fields configured on the index directly from the index and skips a full document lookup. If omitted, the default value is false.
     * @param Optional|Document|Serializable|array|stdClass $nestedOptions Configure how MongoDB Vector Search scores documents that contain nested arrays.
     * @param Optional|QueryInterface|array $parentFilter Any match query that compares an indexed top-level field with a boolean, date, objectId, number, string, or UUID to use as a pre-filter. Only valid if `nestedRoot` is specified in the index definition.
     */
    public function __construct(
        string $index,
        int $limit,
        string $path,
        Binary|PackedArray|BSONArray|array|string $queryVector,
        Optional|bool $exact = Optional::Undefined,
        Optional|QueryInterface|array $filter = Optional::Undefined,
        Optional|int $numCandidates = Optional::Undefined,
        Optional|bool $returnStoredSource = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $nestedOptions = Optional::Undefined,
        Optional|QueryInterface|array $parentFilter = Optional::Undefined,
    ) {
        $this->index = $index;
        $this->limit = $limit;
        $this->path = $path;
        if (is_array($queryVector) && ! array_is_list($queryVector)) {
            throw new InvalidArgumentException('Expected $queryVector argument to be a list, got an associative array.');
        }

        $this->queryVector = $queryVector;
        $this->exact = $exact;
        if (is_array($filter)) {
            $filter = QueryObject::create($filter);
        }

        $this->filter = $filter;
        $this->numCandidates = $numCandidates;
        $this->returnStoredSource = $returnStoredSource;
        $this->nestedOptions = $nestedOptions;
        if (is_array($parentFilter)) {
            $parentFilter = QueryObject::create($parentFilter);
        }

        $this->parentFilter = $parentFilter;
    }
}
