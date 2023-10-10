<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\QueryInterface;
use stdClass;

/**
 * Inverts the effect of a query expression and returns documents that do not match the query expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/not/
 */
class NotOperator implements QueryInterface
{
    public const NAME = '$not';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|QueryInterface|Serializable|array|stdClass $expression */
    public Document|Serializable|QueryInterface|stdClass|array $expression;

    /**
     * @param Document|QueryInterface|Serializable|array|stdClass $expression
     */
    public function __construct(Document|Serializable|QueryInterface|stdClass|array $expression)
    {
        $this->expression = $expression;
    }
}
