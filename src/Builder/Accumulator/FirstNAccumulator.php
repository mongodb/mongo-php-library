<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\WindowInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;
use function str_starts_with;

/**
 * Returns an aggregation of the first n elements within a group.
 * The elements returned are meaningful only if in a specified sort order.
 * If the group contains fewer than n elements, $firstN returns all elements in the group.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/
 * @internal
 */
final class FirstNAccumulator implements AccumulatorInterface, WindowInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$firstN';
    public const PROPERTIES = ['input' => 'input', 'n' => 'n'];

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $input An expression that resolves to the array from which to return n elements. */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input;

    /** @var ResolvesToInt|int|string $n A positive integral expression that is either a constant or depends on the _id value for $group. */
    public readonly ResolvesToInt|int|string $n;

    /**
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $input An expression that resolves to the array from which to return n elements.
     * @param ResolvesToInt|int|string $n A positive integral expression that is either a constant or depends on the _id value for $group.
     */
    public function __construct(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input,
        ResolvesToInt|int|string $n,
    ) {
        $this->input = $input;
        if (is_string($n) && ! str_starts_with($n, '$')) {
            throw new InvalidArgumentException('Argument $n can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->n = $n;
    }
}
