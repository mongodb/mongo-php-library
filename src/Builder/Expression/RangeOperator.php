<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;

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
        $this->start = $start;
        $this->end = $end;
        $this->step = $step;
    }
}
