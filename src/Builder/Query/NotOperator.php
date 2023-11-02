<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use stdClass;

/**
 * Inverts the effect of a query expression and returns documents that do not match the query expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/not/
 */
class NotOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var FieldQueryInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $expression */
    public readonly Type|FieldQueryInterface|stdClass|array|bool|float|int|null|string $expression;

    /**
     * @param FieldQueryInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $expression
     */
    public function __construct(Type|FieldQueryInterface|stdClass|array|bool|float|int|null|string $expression)
    {
        $this->expression = $expression;
    }

    public function getOperator(): string
    {
        return '$not';
    }
}
