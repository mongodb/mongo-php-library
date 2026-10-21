<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\InputStageInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\SearchOperatorInterface;
use stdClass;

/**
 * Returns different types of metadata result documents for the Atlas Search query against an Atlas collection.
 * NOTE: $searchMeta is only available for MongoDB Atlas clusters running MongoDB v4.4.9 or higher, and is not available for self-managed deployments.
 *
 * New in MongoDB 5.0
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/searchMeta/
 * @internal
 */
final class SearchMetaStage implements InputStageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$searchMeta';

    public const PROPERTIES = [
        'operator' => null,
        'index' => 'index',
        'count' => 'count',
        'returnScope' => 'returnScope',
        'returnStoredSource' => 'returnStoredSource',
    ];

    /**
     * @var Document|SearchOperatorInterface|Serializable|array|stdClass $operator Operator to search with.  You can provide a specific operator or use
     * the compound operator to run a compound query with multiple operators.
     */
    public readonly Document|Serializable|SearchOperatorInterface|stdClass|array $operator;

    /** @var Optional|string $index Name of the Atlas Search index to use. If omitted, defaults to default. */
    public readonly Optional|string $index;

    /** @var Optional|Document|Serializable|array|stdClass $count Document that specifies the count options for retrieving a count of the results. */
    public readonly Optional|Document|Serializable|stdClass|array $count;

    /** @var Optional|Document|Serializable|array|stdClass $returnScope Object that sets the context of the query to the specified embedded document field. You must also specify `returnStoredSource` and set it to `true` if your cluster MongoDB version is less than 8.2. */
    public readonly Optional|Document|Serializable|stdClass|array $returnScope;

    /** @var Optional|bool $returnStoredSource Flag that specifies whether to perform a full document lookup on the backend database or return only stored source fields directly from Atlas Search. */
    public readonly Optional|bool $returnStoredSource;

    /**
     * @param Document|SearchOperatorInterface|Serializable|array|stdClass $operator Operator to search with.  You can provide a specific operator or use
     * the compound operator to run a compound query with multiple operators.
     * @param Optional|string $index Name of the Atlas Search index to use. If omitted, defaults to default.
     * @param Optional|Document|Serializable|array|stdClass $count Document that specifies the count options for retrieving a count of the results.
     * @param Optional|Document|Serializable|array|stdClass $returnScope Object that sets the context of the query to the specified embedded document field. You must also specify `returnStoredSource` and set it to `true` if your cluster MongoDB version is less than 8.2.
     * @param Optional|bool $returnStoredSource Flag that specifies whether to perform a full document lookup on the backend database or return only stored source fields directly from Atlas Search.
     */
    public function __construct(
        Document|Serializable|SearchOperatorInterface|stdClass|array $operator,
        Optional|string $index = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $count = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $returnScope = Optional::Undefined,
        Optional|bool $returnStoredSource = Optional::Undefined,
    ) {
        $this->operator = $operator;
        $this->index = $index;
        $this->count = $count;
        $this->returnScope = $returnScope;
        $this->returnStoredSource = $returnStoredSource;
    }
}
