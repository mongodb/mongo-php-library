<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Expression\ResolvesToDate;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\WindowInterface;

/**
 * Returns the average rate of change within the specified window.
 * New in MongoDB 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/derivative/
 */
class DerivativeAccumulator implements WindowInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /** @var Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $input */
    public readonly Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int $input;

    /**
     * @var Optional|string $unit A string that specifies the time unit. Use one of these strings: "week", "day","hour", "minute", "second", "millisecond".
     * If the sortBy field is not a date, you must omit a unit. If you specify a unit, you must specify a date in the sortBy field.
     */
    public readonly Optional|string $unit;

    /**
     * @param Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $input
     * @param Optional|string $unit A string that specifies the time unit. Use one of these strings: "week", "day","hour", "minute", "second", "millisecond".
     * If the sortBy field is not a date, you must omit a unit. If you specify a unit, you must specify a date in the sortBy field.
     */
    public function __construct(
        Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToNumber|float|int $input,
        Optional|string $unit = Optional::Undefined,
    ) {
        $this->input = $input;
        $this->unit = $unit;
    }

    public function getOperator(): string
    {
        return '$derivative';
    }
}
