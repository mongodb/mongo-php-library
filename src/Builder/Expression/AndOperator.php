<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
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
 * @internal
 */
final class AndOperator implements ResolvesToBool, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$and';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var list<DateTimeInterface|Decimal128|ExpressionInterface|Int64|ResolvesToBool|ResolvesToNull|ResolvesToNumber|ResolvesToString|Type|array|bool|float|int|null|stdClass|string> $expression */
    public readonly array $expression;

    /**
     * @param DateTimeInterface|Decimal128|ExpressionInterface|Int64|ResolvesToBool|ResolvesToNull|ResolvesToNumber|ResolvesToString|Type|array|bool|float|int|null|stdClass|string ...$expression
     * @no-named-arguments
     */
    public function __construct(
        DateTimeInterface|Decimal128|Int64|Type|ResolvesToBool|ResolvesToNull|ResolvesToNumber|ResolvesToString|ExpressionInterface|stdClass|array|bool|float|int|null|string ...$expression,
    ) {
        if (\count($expression) < 1) {
            throw new InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }

        if (! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }

        $this->expression = $expression;
    }
}
