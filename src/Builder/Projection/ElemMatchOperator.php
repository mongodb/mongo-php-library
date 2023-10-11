<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Projection;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ProjectionInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use stdClass;

use function is_array;
use function is_object;

/**
 * Projects the first element in an array that matches the specified $elemMatch condition.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/projection/elemMatch/
 */
class ElemMatchOperator implements ProjectionInterface
{
    public const NAME = '$elemMatch';
    public const ENCODE = Encode::Object;

    /** @param Document|QueryInterface|Serializable|array|stdClass $query */
    public Document|Serializable|QueryInterface|stdClass|array $query;

    /**
     * @param Document|QueryInterface|Serializable|array|stdClass $query
     */
    public function __construct(Document|Serializable|QueryInterface|stdClass|array $query)
    {
        if (is_array($query) || is_object($query)) {
            $query = QueryObject::create($query);
        }

        $this->query = $query;
    }
}
