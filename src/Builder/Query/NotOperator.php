<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Document;
use MongoDB\BSON\Regex;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use stdClass;

use function is_array;
use function is_object;

/**
 * Inverts the effect of a query expression and returns documents that do not match the query expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/not/
 */
class NotOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var Document|QueryInterface|Regex|Serializable|array|stdClass $expression */
    public readonly Document|Regex|Serializable|QueryInterface|stdClass|array $expression;

    /**
     * @param Document|QueryInterface|Regex|Serializable|array|stdClass $expression
     */
    public function __construct(Document|Regex|Serializable|QueryInterface|stdClass|array $expression)
    {
        if (is_array($expression) || is_object($expression)) {
            $expression = QueryObject::create(...$expression);
        }

        $this->expression = $expression;
    }

    public function getOperator(): string
    {
        return '$not';
    }
}
