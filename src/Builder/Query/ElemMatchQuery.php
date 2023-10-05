<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use stdClass;

/**
 * Projects the first element in an array that matches the specified $elemMatch condition.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/elemMatch/
 */
class ElemMatchQuery implements QueryInterface
{
    public const NAME = '$elemMatch';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Document|Serializable|array|stdClass $queries */
    public Document|Serializable|stdClass|array $queries;

    /**
     * @param Document|Serializable|array|stdClass $queries
     */
    public function __construct(Document|Serializable|stdClass|array $queries)
    {
        $this->queries = $queries;
    }
}
