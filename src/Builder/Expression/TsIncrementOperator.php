<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Timestamp;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Returns the incrementing ordinal from a timestamp as a long.
 * New in MongoDB 5.1.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsIncrement/
 */
class TsIncrementOperator implements ResolvesToLong, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var ResolvesToTimestamp|Timestamp|int $expression */
    public readonly Timestamp|ResolvesToTimestamp|int $expression;

    /**
     * @param ResolvesToTimestamp|Timestamp|int $expression
     */
    public function __construct(Timestamp|ResolvesToTimestamp|int $expression)
    {
        $this->expression = $expression;
    }

    public function getOperator(): string
    {
        return '$tsIncrement';
    }
}
