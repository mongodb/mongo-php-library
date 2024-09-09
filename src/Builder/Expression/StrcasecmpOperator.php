<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Performs case-insensitive string comparison and returns: 0 if two strings are equivalent, 1 if the first string is greater than the second, and -1 if the first string is less than the second.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strcasecmp/
 */
class StrcasecmpOperator implements ResolvesToInt, OperatorInterface
{
    public const ENCODE = Encode::Array;

    /** @var ResolvesToString|string $expression1 */
    public readonly ResolvesToString|string $expression1;

    /** @var ResolvesToString|string $expression2 */
    public readonly ResolvesToString|string $expression2;

    /**
     * @param ResolvesToString|string $expression1
     * @param ResolvesToString|string $expression2
     */
    public function __construct(ResolvesToString|string $expression1, ResolvesToString|string $expression2)
    {
        $this->expression1 = $expression1;
        $this->expression2 = $expression2;
    }

    public function getOperator(): string
    {
        return '$strcasecmp';
    }
}
