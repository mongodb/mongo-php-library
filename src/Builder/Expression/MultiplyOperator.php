<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Exception\InvalidArgumentException;

use function array_is_list;

/**
 * Multiplies numbers to return the product. Accepts any number of argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/multiply/
 */
class MultiplyOperator implements ResolvesToDecimal
{
    public const NAME = '$multiply';
    public const ENCODE = Encode::Single;

    /**
     * @param list<Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int> ...$expression The arguments can be any valid expression as long as they resolve to numbers.
     * Starting in MongoDB 6.1 you can optimize the $multiply operation. To improve performance, group references at the end of the argument list.
     */
    public array $expression;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int ...$expression The arguments can be any valid expression as long as they resolve to numbers.
     * Starting in MongoDB 6.1 you can optimize the $multiply operation. To improve performance, group references at the end of the argument list.
     * @no-named-arguments
     */
    public function __construct(Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }
        $this->expression = $expression;
    }
}
