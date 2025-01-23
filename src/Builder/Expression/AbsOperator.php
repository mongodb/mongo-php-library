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
 * Returns the absolute value of a number.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/abs/
 * @internal
 */
final class AbsOperator implements ResolvesToNumber, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$abs';
    public const PROPERTIES = ['value' => 'value'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $value */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $value;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $value
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int|string $value)
    {
        $this->value = $value;
    }
}
