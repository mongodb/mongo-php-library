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

use function is_string;
use function str_starts_with;

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
        if (is_string($exponent) && ! str_starts_with($exponent, '$')) {
            throw new InvalidArgumentException('Argument $exponent can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->exponent = $exponent;
    }
}
