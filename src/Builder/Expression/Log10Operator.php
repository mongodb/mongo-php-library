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

/**
 * Calculates the log base 10 of a number.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/log10/
 * @internal
 */
final class Log10Operator implements ResolvesToDouble, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$log10';
    public const PROPERTIES = ['number' => 'number'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $number Any valid expression as long as it resolves to a non-negative number. */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $number;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $number Any valid expression as long as it resolves to a non-negative number.
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int|string $number)
    {
        $this->number = $number;
    }
}
