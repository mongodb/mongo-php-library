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
 * Raises e to the specified exponent.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/exp/
 * @internal
 */
final class ExpOperator implements ResolvesToDouble, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$exp';
    public const PROPERTIES = ['exponent' => 'exponent'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $exponent */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $exponent;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $exponent
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int|string $exponent)
    {
        $this->exponent = $exponent;
    }
}
