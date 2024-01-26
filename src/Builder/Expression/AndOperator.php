<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function array_is_list;

/**
 * Returns true only when all its expressions evaluate to true. Accepts any number of argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/and/
 */
class AndOperator implements ResolvesToBool, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var list<Decimal128|ExpressionInterface|Int64|ResolvesToBool|ResolvesToNull|ResolvesToNumber|ResolvesToString|Type|array|bool|float|int|null|stdClass|string> $expression */
    public readonly array $expression;

    /**
     * @param Decimal128|ExpressionInterface|Int64|ResolvesToBool|ResolvesToNull|ResolvesToNumber|ResolvesToString|Type|array|bool|float|int|null|stdClass|string ...$expression
     * @no-named-arguments
     */
    public function __construct(
        Decimal128|Int64|Type|ResolvesToBool|ResolvesToNull|ResolvesToNumber|ResolvesToString|ExpressionInterface|stdClass|array|bool|float|int|null|string ...$expression,
    ) {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }

        if (! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }

        $this->expression = $expression;
    }

    public function getOperator(): string
    {
        return '$and';
    }
}
