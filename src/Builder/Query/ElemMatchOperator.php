<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\ProjectionInterface;
use MongoDB\Builder\Type\QueryFilterInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use stdClass;

use function is_array;
use function is_object;

/**
 * Projects the first element in an array that matches the specified $elemMatch condition.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/elemMatch/
 */
class ElemMatchOperator implements QueryFilterInterface, ProjectionInterface
{
    public const NAME = '$elemMatch';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Document|QueryInterface|Serializable|array|stdClass $queries */
    public Document|Serializable|QueryInterface|stdClass|array $queries;

    /**
     * @param Document|QueryInterface|Serializable|array|stdClass $queries
     */
    public function __construct(Document|Serializable|QueryInterface|stdClass|array $queries)
    {
        if (is_array($queries) || is_object($queries)) {
            $queries = QueryObject::create($queries);
        }

        $this->queries = $queries;
    }
}
