<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use stdClass;

/**
 * Return a value without parsing. Use for values that the aggregation pipeline may interpret as an expression. For example, use a $literal expression to a string that starts with a dollar sign ($) to avoid parsing as a field path.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/literal/
 */
readonly class LiteralOperator implements ResolvesToAny
{
    public const NAME = '$literal';
    public const ENCODE = Encode::Single;

    /** @param Type|array|bool|float|int|non-empty-string|null|stdClass $value If the value is an expression, $literal does not evaluate the expression but instead returns the unparsed expression. */
    public Type|stdClass|array|bool|float|int|null|string $value;

    /**
     * @param Type|array|bool|float|int|non-empty-string|null|stdClass $value If the value is an expression, $literal does not evaluate the expression but instead returns the unparsed expression.
     */
    public function __construct(Type|stdClass|array|bool|float|int|null|string $value)
    {
        $this->value = $value;
    }
}
