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
 * Returns different types of metadata result documents for the Atlas Search query against an Atlas collection.
 * NOTE: $searchMeta is only available for MongoDB Atlas clusters running MongoDB v4.4.9 or higher, and is not available for self-managed deployments.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/searchMeta/
 * @internal
 */
final class SearchMetaStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$searchMeta';
    public const PROPERTIES = ['operator' => null, 'index' => 'index', 'count' => 'count'];

    /**
     * @var Document|SearchOperatorInterface|Serializable|array|stdClass $operator Operator to search with.  You can provide a specific operator or use
     * the compound operator to run a compound query with multiple operators.
     */
    public readonly Document|Serializable|SearchOperatorInterface|stdClass|array $operator;

    /** @var Optional|string $index Name of the Atlas Search index to use. If omitted, defaults to default. */
    public readonly Optional|string $index;

    /** @var Optional|Document|Serializable|array|stdClass $count Document that specifies the count options for retrieving a count of the results. */
    public readonly Optional|Document|Serializable|stdClass|array $count;

    /**
     * @param Document|SearchOperatorInterface|Serializable|array|stdClass $operator Operator to search with.  You can provide a specific operator or use
     * the compound operator to run a compound query with multiple operators.
     * @param Optional|string $index Name of the Atlas Search index to use. If omitted, defaults to default.
     * @param Optional|Document|Serializable|array|stdClass $count Document that specifies the count options for retrieving a count of the results.
     */
    public function __construct(
        Document|Serializable|SearchOperatorInterface|stdClass|array $operator,
        Optional|string $index = Optional::Undefined,
        Optional|Document|Serializable|stdClass|array $count = Optional::Undefined,
    ) {
        $this->operator = $operator;
        $this->index = $index;
        $this->count = $count;
    }
}
