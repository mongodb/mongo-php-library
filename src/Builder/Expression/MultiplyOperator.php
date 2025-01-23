<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;

use function array_is_list;

/**
 * Multiplies numbers to return the product. Accepts any number of argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/multiply/
 * @internal
 */
final class MultiplyOperator implements ResolvesToDecimal, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$multiply';
    public const PROPERTIES = ['expression' => 'expression'];

    /**
     * @var list<Decimal128|Int64|ResolvesToNumber|float|int|string> $expression The arguments can be any valid expression as long as they resolve to numbers.
     * Starting in MongoDB 6.1 you can optimize the $multiply operation. To improve performance, group references at the end of the argument list.
     */
    public readonly array $expression;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string ...$expression The arguments can be any valid expression as long as they resolve to numbers.
     * Starting in MongoDB 6.1 you can optimize the $multiply operation. To improve performance, group references at the end of the argument list.
     * @no-named-arguments
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int|string ...$expression)
    {
        if (\count($expression) < 1) {
            throw new InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }

        if (! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }

        $this->expression = $expression;
    }
}
