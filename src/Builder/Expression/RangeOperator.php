<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

/**
 * Outputs an array containing a sequence of integers according to user-defined inputs.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/range/
 * @internal
 */
final class RangeOperator implements ResolvesToArray, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$range';
    public const PROPERTIES = ['start' => 'start', 'end' => 'end', 'step' => 'step'];

    /** @var ResolvesToInt|int|string $start An integer that specifies the start of the sequence. Can be any valid expression that resolves to an integer. */
    public readonly ResolvesToInt|int|string $start;

    /** @var ResolvesToInt|int|string $end An integer that specifies the exclusive upper limit of the sequence. Can be any valid expression that resolves to an integer. */
    public readonly ResolvesToInt|int|string $end;

    /** @var Optional|ResolvesToInt|int|string $step An integer that specifies the increment value. Can be any valid expression that resolves to a non-zero integer. Defaults to 1. */
    public readonly Optional|ResolvesToInt|int|string $step;

    /**
     * @param ResolvesToInt|int|string $start An integer that specifies the start of the sequence. Can be any valid expression that resolves to an integer.
     * @param ResolvesToInt|int|string $end An integer that specifies the exclusive upper limit of the sequence. Can be any valid expression that resolves to an integer.
     * @param Optional|ResolvesToInt|int|string $step An integer that specifies the increment value. Can be any valid expression that resolves to a non-zero integer. Defaults to 1.
     */
    public function __construct(
        ResolvesToInt|int|string $start,
        ResolvesToInt|int|string $end,
        Optional|ResolvesToInt|int|string $step = Optional::Undefined,
    ) {
        if (is_string($start) && ! str_starts_with($start, '$')) {
            throw new InvalidArgumentException('Argument $start can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->start = $start;
        if (is_string($end) && ! str_starts_with($end, '$')) {
            throw new InvalidArgumentException('Argument $end can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->end = $end;
        if (is_string($step) && ! str_starts_with($step, '$')) {
            throw new InvalidArgumentException('Argument $step can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->step = $step;
    }
}
