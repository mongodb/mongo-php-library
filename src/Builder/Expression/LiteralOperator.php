<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use stdClass;

/**
 * Return a value without parsing. Use for values that the aggregation pipeline may interpret as an expression. For example, use a $literal expression to a string that starts with a dollar sign ($) to avoid parsing as a field path.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/literal/
 * @internal
 */
final class LiteralOperator implements ResolvesToAny, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$literal';
    public const PROPERTIES = ['value' => 'value'];

    /** @var DateTimeInterface|Type|array|bool|float|int|null|stdClass|string $value If the value is an expression, $literal does not evaluate the expression but instead returns the unparsed expression. */
    public readonly DateTimeInterface|Type|stdClass|array|bool|float|int|null|string $value;

    /**
     * @param DateTimeInterface|Type|array|bool|float|int|null|stdClass|string $value If the value is an expression, $literal does not evaluate the expression but instead returns the unparsed expression.
     */
    public function __construct(DateTimeInterface|Type|stdClass|array|bool|float|int|null|string $value)
    {
        $this->value = $value;
    }
}
