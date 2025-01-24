<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\SearchOperatorInterface;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

/**
 * Performs a full-text search of the field or fields in an Atlas collection.
 * NOTE: $search is only available for MongoDB Atlas clusters, and is not available for self-managed deployments.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/search/
 * @internal
 */
final class SearchStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$search';

    public const PROPERTIES = [
        'operator' => null,
        'index' => 'index',
        'highlight' => 'highlight',
        'concurrent' => 'concurrent',
        'count' => 'count',
        'searchAfter' => 'searchAfter',
        'searchBefore' => 'searchBefore',
        'scoreDetails' => 'scoreDetails',
        'sort' => 'sort',
        'returnStoredSource' => 'returnStoredSource',
        'tracking' => 'tracking',
    ];

    /**
     * @var Document|SearchOperatorInterface|Serializable|array|stdClass $operator Operator to search with.  You can provide a specific operator or use
     * the compound operator to run a compound query with multiple operators.
     */
    public readonly Document|Serializable|SearchOperatorInterface|stdClass|array $operator;

    /** @var Optional|string $index Name of the Atlas Search index to use. If omitted, defaults to "default". */
    public readonly Optional|string $index;

    /** @var Optional|Document|Serializable|array|stdClass $highlight Specifies the highlighting options for displaying search terms in their original context. */
    public readonly Optional|Document|Serializable|stdClass|array $highlight;

    /**
     * @var Optional|bool $concurrent Parallelize search across segments on dedicated search nodes.
     * If you don't have separate search nodes on your cluster,
     * Atlas Search ignores this flag. If omitted, defaults to false.
     */
    public readonly Optional|bool $concurrent;

    /** @var Optional|Document|Serializable|array|stdClass $count Document that specifies the count options for retrieving a count of the results. */
    public readonly Optional|Document|Serializable|stdClass|array $count;

    /** @var Optional|string $searchAfter Reference point for retrieving results. searchAfter returns documents starting immediately following the specified reference point. */
    public readonly Optional|string $searchAfter;

    /** @var Optional|string $searchBefore Reference point for retrieving results. searchBefore returns documents starting immediately before the specified reference point. */
    public readonly Optional|string $searchBefore;

    /** @var Optional|bool $scoreDetails Flag that specifies whether to retrieve a detailed breakdown of the score for the documents in the results. If omitted, defaults to false. */
    public readonly Optional|bool $scoreDetails;

    /** @var Optional|Document|Serializable|array|stdClass $sort Document that specifies the fields to sort the Atlas Search results by in ascending or descending order. */
    public readonly Optional|Document|Serializable|stdClass|array $sort;

    /** @var Optional|bool $returnStoredSource Flag that specifies whether to perform a full document lookup on the backend database or return only stored source fields directly from Atlas Search. */
    public readonly Optional|bool $returnStoredSource;

    /** @var Optional|Document|Serializable|array|stdClass $tracking Document that specifies the tracking option to retrieve analytics information on the search terms. */
    public readonly Optional|Document|Serializable|stdClass|array $tracking;

    /**
     * @param Document|SearchOperatorInterface|Serializable|array|stdClass $operator Operator to search with.  You can provide a specific operator or use
     * the compound operator to run a compound query with multiple operators.
     * @param Optional|string $index Name of the Atlas Search index to use. If omitted, defaults to "default".
     * @param Optional|Document|Serializable|array|stdClass $highlight Specifies the highlighting options for displaying search terms in their original context.
     * @param Optional|bool $concurrent Parallelize search across segments on dedicated search nodes.
     * If you don't have separate search nodes on your cluster,
     * Atlas Search ignores this flag. If omitted, defaults to false.
     * @param Optional|Document|Serializable|array|stdClass $count Document that specifies the count options for retrieving a count of the results.
     * @param Optional|string $searchAfter Reference point for retrieving results. searchAfter returns documents starting immediately following the specified reference point.
     * @param Optional|string $searchBefore Reference point for retrieving results. searchBefore returns documents starting immediately before the specified reference point.
     * @param Optional|bool $scoreDetails Flag that specifies whether to retrieve a detailed breakdown of the score for the documents in the results. If omitted, defaults to false.
     * @param Optional|Document|Serializable|array|stdClass $sort Document that specifies the fields to sort the Atlas Search results by in ascending or descending order.
     * @param Optional|bool $returnStoredSource Flag that specifies whether to perform a full document lookup on the backend database or return only stored source fields directly from Atlas Search.
     * @param Optional|Document|Serializable|array|stdClass $tracking Document that specifies the tracking option to retrieve analytics information on the search terms.
     */
    public function __construct(
        Document|Serializable|SearchOperatorInterface|stdClass|array $operator,
        Optional|string $index = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $highlight = Optional::Undefined,
        Optional|bool $concurrent = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $count = Optional::Undefined,
        Optional|string $searchAfter = Optional::Undefined,
        Optional|string $searchBefore = Optional::Undefined,
        Optional|bool $scoreDetails = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $sort = Optional::Undefined,
        Optional|bool $returnStoredSource = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $tracking = Optional::Undefined,
    ) {
        $this->operator = $operator;
        $this->index = $index;
        $this->highlight = $highlight;
        $this->concurrent = $concurrent;
        $this->count = $count;
        $this->searchAfter = $searchAfter;
        $this->searchBefore = $searchBefore;
        $this->scoreDetails = $scoreDetails;
        $this->sort = $sort;
        $this->returnStoredSource = $returnStoredSource;
        $this->tracking = $tracking;
    }
}
